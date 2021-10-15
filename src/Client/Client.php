<?php declare(strict_types = 1);

/**
 * Client.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:MqttConnectorPlugin!
 * @subpackage     Client
 * @since          0.1.0
 *
 * @date           15.10.21
 */

namespace FastyBird\MqttConnectorPlugin\Client;

use BinSoul\Net\Mqtt;
use FastyBird\MqttConnectorPlugin\Constants;
use Nette;
use Psr\Log;
use SplObjectStorage;
use Throwable;

/**
 * MQTT clients proxy
 *
 * @package        FastyBird:MqttConnectorPlugin!
 * @subpackage     Client
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class Client
{

	use Nette\SmartObject;

	/** @var SplObjectStorage<MqttClient, null> */
	private SplObjectStorage $clients;

	/** @var MqttClientFactory */
	private MqttClientFactory $mqttClientFactory;

	/** @var Log\LoggerInterface */
	private Log\LoggerInterface $logger;

	public function __construct(
		MqttClientFactory $mqttClientFactory,
		?Log\LoggerInterface $logger = null
	) {
		$this->mqttClientFactory = $mqttClientFactory;
		$this->logger = $logger ?? new Log\NullLogger();

		$this->clients = new SplObjectStorage();
	}

	/**
	 * @param ConnectionSettings[] $settings
	 */
	public function initialize(array $settings): void
	{
		foreach ($settings as $setting) {
			$client = $this->mqttClientFactory->create($setting);

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

			$this->clients->attach($client);
		}
	}

	/**
	 * @param string $topic
	 * @param string|null $payload
	 * @param int $qos
	 * @param bool $retained
	 *
	 * @return void
	 */
	public function publish(
		string $topic,
		?string $payload = null,
		int $qos = Constants::MQTT_API_QOS_0,
		bool $retained = false
	): void {
		$message = new Mqtt\DefaultMessage(
			$topic,
			$payload ?? '',
			$qos,
			$retained
		);

		$this->clients->rewind();

		/** @var MqttClient $client */
		foreach ($this->clients as $client) {
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
