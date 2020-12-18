<?php declare(strict_types = 1);

/**
 * MqttClientMessageHandler.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:MqttPlugin!
 * @subpackage     Events
 * @since          0.1.0
 *
 * @date           16.04.20
 */

namespace FastyBird\MqttPlugin\Events;

use BinSoul\Net\Mqtt;
use FastyBird\MqttPlugin\Consumers;
use FastyBird\MqttPlugin\Entities;
use IPub\MQTTClient;
use Nette;
use Psr\Log;

/**
 * MQTT client received message event handler
 *
 * @package         FastyBird:MqttPlugin!
 * @subpackage      Events
 *
 * @author          Adam Kadlec <adam.kadlec@fastybird.com>
 */
class MqttClientMessageHandler
{

	use Nette\SmartObject;

	/**
	 * When new client is connected, broker send specific payload
	 */
	private const NEW_CLIENT_MESSAGE_PAYLOAD = 'New client connected from';

	/** @var Consumers\ExchangeConsumer */
	private Consumers\ExchangeConsumer $consumer;

	/** @var Log\LoggerInterface */
	private Log\LoggerInterface $logger;

	public function __construct(
		Consumers\ExchangeConsumer $consumer,
		?Log\LoggerInterface $logger = null
	) {
		$this->consumer = $consumer;
		$this->logger = $logger ?? new Log\NullLogger();
	}

	/**
	 * @param Mqtt\Message $message
	 * @param MQTTClient\Client\IClient $client
	 *
	 * @return void
	 */
	public function __invoke(Mqtt\Message $message, MQTTClient\Client\IClient $client): void
	{
		// Broker send message
		$this->logger->info(sprintf('[FB:PLUGIN:MQTT] Received message in topic: %s with payload %s', $message->getTopic(), $message->getPayload()), [
			'server'  => [
				'uri'  => $client->getUri(),
				'port' => $client->getPort(),
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
								$entity = new Entities\DeviceProperty($deviceId, 'ip-address');
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

}
