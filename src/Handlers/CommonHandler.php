<?php declare(strict_types = 1);

/**
 * CommonHandler.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:FbMqttConnectorPlugin!
 * @subpackage     Handlers
 * @since          0.1.0
 *
 * @date           15.10.21
 */

namespace FastyBird\MqttConnectorPlugin\Handlers;

use BinSoul\Net\Mqtt;
use FastyBird\MqttConnectorPlugin\Client;
use FastyBird\MqttConnectorPlugin\Consumers;
use FastyBird\MqttConnectorPlugin\Entities;
use Nette;
use Psr\Log;
use Throwable;

/**
 * MQTT client common handler
 *
 * @package         FastyBird:FbMqttConnectorPlugin!
 * @subpackage      Handlers
 *
 * @author          Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class CommonHandler implements IHandler
{

	use Nette\SmartObject;

	public const MQTT_SYSTEM_TOPIC = '$SYS/broker/log/#';

	/**
	 * When new client is connected, broker send specific payload
	 */
	private const NEW_CLIENT_MESSAGE_PAYLOAD = 'New client connected from';

	/** @var Consumers\IConsumer */
	private Consumers\IConsumer $consumer;

	/** @var Log\LoggerInterface */
	private Log\LoggerInterface $logger;

	public function __construct(
		Consumers\IConsumer $consumer,
		?Log\LoggerInterface $logger = null
	) {
		$this->consumer = $consumer;
		$this->logger = $logger ?? new Log\NullLogger();
	}

	/**
	 * {@inheritDoc}
	 */
	public function onOpen(Mqtt\Connection $connection, Client\MqttClient $client): void
	{
		// Network connection established
		$this->logger->info(sprintf('[FB:PLUGIN:MQTT] Established connection to MQTT broker: %s', $client->getHost()), [
			'server'      => [
				'uri'      => $client->getHost(),
				'port'     => $client->getPort(),
				'clientId' => $client->getClientId()->toString(),
			],
			'credentials' => [
				'username' => $connection->getUsername(),
			],
		]);
	}

	/**
	 * {@inheritDoc}
	 */
	public function onClose(Mqtt\Connection $connection, Client\MqttClient $client): void
	{
		// Network connection closed
		$this->logger->info(sprintf('[FB:PLUGIN:MQTT] Connection to MQTT broker: %s was closed', $client->getHost()), [
			'server'      => [
				'uri'      => $client->getHost(),
				'port'     => $client->getPort(),
				'clientId' => $client->getClientId()->toString(),
			],
			'credentials' => [
				'username' => $connection->getUsername(),
			],
		]);

		$client->getLoop()->stop();
	}

	/**
	 * {@inheritDoc}
	 */
	public function onConnect(Mqtt\Connection $connection, Client\MqttClient $client): void
	{
		// Broker connected
		$this->logger->info(sprintf('[FB:PLUGIN:MQTT] Connected to MQTT broker with client id %s', $client->getClientId()->toString()), [
			'server'      => [
				'uri'      => $client->getHost(),
				'port'     => $client->getPort(),
				'clientId' => $client->getClientId()->toString(),
			],
			'credentials' => [
				'username' => $connection->getUsername(),
			],
		]);

		$topic = new Mqtt\DefaultSubscription(self::MQTT_SYSTEM_TOPIC);

		// ...& subscribe to them
		$client
			->subscribe($topic)
			->done(
				function (Mqtt\Subscription $subscription): void {
					$this->logger->info(sprintf('[FB:PLUGIN:MQTT] Subscribed to: %s', $subscription->getFilter()));
				},
				function (Throwable $ex): void {
					$this->logger->error('[FB:PLUGIN:MQTT] ' . $ex->getMessage(), [
						'exception' => [
							'message' => $ex->getMessage(),
							'code'    => $ex->getCode(),
						],
					]);
				}
			);
	}

	/**
	 * {@inheritDoc}
	 */
	public function onDisconnect(Mqtt\Connection $connection, Client\MqttClient $client): void
	{
		// Broker disconnected
		$this->logger->info(sprintf('[FB:PLUGIN:MQTT] Disconnected from MQTT broker with client id %s', $client->getClientId()->toString()), [
			'server'      => [
				'uri'      => $client->getHost(),
				'port'     => $client->getPort(),
				'clientId' => $client->getClientId()->toString(),
			],
			'credentials' => [
				'username' => $connection->getUsername(),
			],
		]);
	}

	/**
	 * {@inheritDoc}
	 */
	public function onWarning(Throwable $ex, Client\MqttClient $client): void
	{
		// Broker warning occur
		$this->logger->warning(sprintf('[FB:PLUGIN:MQTT] There was an error  %s', $ex->getMessage()), [
			'server' => [
				'uri'      => $client->getHost(),
				'port'     => $client->getPort(),
				'clientId' => $client->getClientId()->toString(),
			],
			'error'  => [
				'message' => $ex->getMessage(),
				'code'    => $ex->getCode(),
			],
		]);

		$client->getLoop()->stop();
	}

	/**
	 * {@inheritDoc}
	 */
	public function onError(Throwable $ex, Client\MqttClient $client): void
	{
		// Broker error occur
		$this->logger->error(sprintf('[FB:PLUGIN:MQTT] There was an error  %s', $ex->getMessage()), [
			'server' => [
				'uri'      => $client->getHost(),
				'port'     => $client->getPort(),
				'clientId' => $client->getClientId()->toString(),
			],
			'error'  => [
				'message' => $ex->getMessage(),
				'code'    => $ex->getCode(),
			],
		]);

		$client->getLoop()->stop();
	}

	/**
	 * {@inheritDoc}
	 */
	public function onMessage(Mqtt\Message $message, Client\MqttClient $client): void
	{
		// Broker send message
		$this->logger->info(sprintf('[FB:PLUGIN:MQTT] Received message in topic: %s with payload %s', $message->getTopic(), $message->getPayload()), [
			'server'  => [
				'uri'      => $client->getHost(),
				'port'     => $client->getPort(),
				'clientId' => $client->getClientId()->toString(),
			],
			'message' => [
				'topic'      => $message->getTopic(),
				'payload'    => $message->getPayload(),
				'isRetained' => $message->isRetained(),
				'qos'        => $message->getQosLevel(),
			],
		]);

		// Check for broker system topic
		if (strpos($message->getTopic(), '$SYS') !== false) {
			[, $param1, $param2, $param3] = explode('/', $message->getTopic()) + [null, null, null, null];

			$payload = $message->getPayload();

			// Broker log
			if ($param1 === 'broker' && $param2 === 'log') {
				switch ($param3) {
					// Notice
					case 'N':
						$this->logger->notice('[FB:PLUGIN:MQTT] ' . $payload);

						// Nev device connected message
						if (strpos($message->getPayload(), self::NEW_CLIENT_MESSAGE_PAYLOAD) !== false) {
							[, , , , , $ipAddress, , $deviceId, , , $username] = explode(' ', $message->getPayload()) + [null, null, null, null, null, null, null, null, null, null, null];

							// Check for correct data
							if ($username !== null && $deviceId !== null && $ipAddress !== null) {
								$entity = new Entities\DeviceProperty(
									$client->getClientId(),
									$deviceId,
									'ip-address'
								);
								$entity->setValue($ipAddress);

								$this->consumer->consume($entity);
							}
						}

						break;

					// Error
					case 'E':
						$this->logger->error('[FB:PLUGIN:MQTT] ' . $payload);
						break;

					// Information
					case 'I':
						$this->logger->info('[FB:PLUGIN:MQTT] ' . $payload);
						break;

					default:
						$this->logger->debug('[FB:PLUGIN:MQTT] ' . $param3 . ': ' . $payload);
						break;
				}
			}
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function onSubscribe(Mqtt\Subscription $subscription, Client\MqttClient $client): void
	{
		// TODO: Implement onSubscribe() method.
	}

	/**
	 * {@inheritDoc}
	 */
	public function onUnsubscribe(array $subscriptions, Client\MqttClient $client): void
	{
		// TODO: Implement onUnsubscribe() method.
	}

	/**
	 * {@inheritDoc}
	 */
	public function onPublish(Mqtt\Message $message, Client\MqttClient $client): void
	{
		// TODO: Implement onPublish() method.
	}

}
