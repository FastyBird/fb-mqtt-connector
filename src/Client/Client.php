<?php declare(strict_types = 1);

/**
 * Client.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:FbMqttConnectorPlugin!
 * @subpackage     Client
 * @since          0.1.0
 *
 * @date           15.10.21
 */

namespace FastyBird\MqttConnectorPlugin\Client;

use BinSoul\Net\Mqtt;
use FastyBird\MqttConnectorPlugin\Constants;
use FastyBird\MqttConnectorPlugin\Exceptions\InvalidStateException;
use Nette;
use Psr\Log;
use Ramsey\Uuid;
use SplObjectStorage;
use Throwable;

/**
 * MQTT clients proxy
 *
 * @package        FastyBird:FbMqttConnectorPlugin!
 * @subpackage     Client
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class Client
{

	use Nette\SmartObject;

	/** @var SplObjectStorage<MqttClient, null> */
	private SplObjectStorage $clients;

	/** @var Log\LoggerInterface */
	private Log\LoggerInterface $logger;

	public function __construct(
		?Log\LoggerInterface $logger = null
	) {
		$this->logger = $logger ?? new Log\NullLogger();

		$this->clients = new SplObjectStorage();
	}

	/**
	 * @param Uuid\UuidInterface|Uuid\UuidInterface[]|null $clientId
	 *
	 * @return void
	 */
	public function connectClient($clientId = null): void
	{
		$processClientsIds = $this->buildClientsIdsList($clientId);

		foreach ($this->getClients() as $client) {
			if ($processClientsIds === [] || in_array($client->getClientId(), $processClientsIds, true)) {
				if (!$client->isEnabled()) {
					throw new InvalidStateException('Client is not enabled and can not be connected');
				}

				try {
					$client->connect()
						->otherwise(function (Throwable $ex) use ($client): void {
							// Log error action reason
							$this->logger->error('[FB:PLUGIN:MQTT] Stopping MQTT client', [
								'exception' => [
									'message' => $ex->getMessage(),
									'code'    => $ex->getCode(),
								],
							]);

							$client->getLoop()
								->stop();
						});

				} catch (Throwable $ex) {
					// Log error action reason
					$this->logger->error('[FB:PLUGIN:MQTT] Stopping MQTT client', [
						'exception' => [
							'message' => $ex->getMessage(),
							'code'    => $ex->getCode(),
						],
					]);

					$client->getLoop()
						->stop();
				}
			}
		}
	}

	/**
	 * @param MqttClient $client
	 */
	public function registerClient(MqttClient $client): void
	{
		$this->clients->attach($client);
	}

	/**
	 * @param Uuid\UuidInterface $clientId
	 *
	 * @return bool
	 */
	public function removeClient(Uuid\UuidInterface $clientId): bool
	{
		foreach ($this->getClients() as $client) {
			if ($clientId->equals($client->getClientId())) {
				$this->clients->detach($client);

				return true;
			}
		}

		return false;
	}

	/**
	 * @return void
	 */
	public function resetClients(): void
	{
		foreach ($this->getClients() as $client) {
			$client->disconnect();
		}

		$this->clients = new SplObjectStorage();
	}

	/**
	 * @param Uuid\UuidInterface|Uuid\UuidInterface[]|null $clientId
	 *
	 * @return bool
	 */
	public function enableClient($clientId = null): bool
	{
		$processClientsIds = $this->buildClientsIdsList($clientId);

		$result = false;

		foreach ($this->getClients() as $client) {
			if ($processClientsIds === [] || in_array($client->getClientId(), $processClientsIds, true)) {
				$result = $client->enable();
			}
		}

		return $result;
	}

	/**
	 * @param Uuid\UuidInterface|Uuid\UuidInterface[]|null $clientId
	 *
	 * @return bool
	 */
	public function disabledClient($clientId = null): bool
	{
		$processClientsIds = $this->buildClientsIdsList($clientId);

		$result = false;

		foreach ($this->getClients() as $client) {
			if ($processClientsIds === [] || in_array($client->getClientId(), $processClientsIds, true)) {
				$result = $client->disable();
			}
		}

		return $result;
	}

	/**
	 * @param string $topic
	 * @param string|null $payload
	 * @param int $qos
	 * @param bool $retained
	 * @param Uuid\UuidInterface|null $clientId
	 *
	 * @return void
	 */
	public function publish(
		string $topic,
		?string $payload = null,
		int $qos = Constants::MQTT_API_QOS_0,
		bool $retained = false,
		?Uuid\UuidInterface $clientId = null
	): void {
		$message = new Mqtt\DefaultMessage(
			$topic,
			$payload ?? '',
			$qos,
			$retained
		);

		foreach ($this->getClients() as $client) {
			if ($clientId === null || $client->getClientId()->equals($clientId)) {
				$client->publish($message)
					->otherwise(function (Throwable $ex) use ($topic, $payload, $qos, $retained): void {
						$this->logger->error('[FB:PLUGIN:MQTT] Message could not be published', [
							'message'   => [
								'topic'    => $topic,
								'payload'  => $payload,
								'qos'      => $qos,
								'retained' => $retained,
							],
							'exception' => [
								'message' => $ex->getMessage(),
								'code'    => $ex->getCode(),
							],
						]);
					});
			}
		}
	}

	/**
	 * @return SplObjectStorage<MqttClient, null>
	 */
	public function getClients(): SplObjectStorage
	{
		$this->clients->rewind();

		return $this->clients;
	}

	/**
	 * @param Uuid\UuidInterface|Uuid\UuidInterface[]|null $clientId
	 *
	 * @return Uuid\UuidInterface[]
	 */
	private function buildClientsIdsList($clientId = null): array
	{
		$processClientsIds = [];

		if ($clientId instanceof Uuid\UuidInterface) {
			$processClientsIds[] = $clientId;

		} elseif (is_array($clientId)) {
			$processClientsIds = $clientId;
		}

		return $processClientsIds;
	}

}
