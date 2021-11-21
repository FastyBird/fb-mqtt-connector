<?php declare(strict_types = 1);

/**
 * ApiV1Handler.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:MqttConnectorPlugin!
 * @subpackage     Handlers
 * @since          0.1.0
 *
 * @date           15.10.21
 */

namespace FastyBird\MqttConnectorPlugin\Handlers;

use BinSoul\Net\Mqtt;
use FastyBird\MqttConnectorPlugin;
use FastyBird\MqttConnectorPlugin\API;
use FastyBird\MqttConnectorPlugin\Client;
use FastyBird\MqttConnectorPlugin\Consumers;
use FastyBird\MqttConnectorPlugin\Exceptions;
use Nette;
use Psr\Log;
use Ramsey\Uuid;
use Throwable;

/**
 * MQTT client API v1 handler
 *
 * @package         FastyBird:MqttConnectorPlugin!
 * @subpackage      Handlers
 *
 * @author          Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class ApiV1Handler implements IHandler
{

	use Nette\SmartObject;

	// MQTT api topics subscribe format
	public const DEVICES_TOPICS = [
		MqttConnectorPlugin\Constants::MQTT_API_PREFIX . MqttConnectorPlugin\Constants::MQTT_API_V1_VERSION_PREFIX . '/+/+',
		MqttConnectorPlugin\Constants::MQTT_API_PREFIX . MqttConnectorPlugin\Constants::MQTT_API_V1_VERSION_PREFIX . '/+/+/+',
		MqttConnectorPlugin\Constants::MQTT_API_PREFIX . MqttConnectorPlugin\Constants::MQTT_API_V1_VERSION_PREFIX . '/+/+/+/+',
		MqttConnectorPlugin\Constants::MQTT_API_PREFIX . MqttConnectorPlugin\Constants::MQTT_API_V1_VERSION_PREFIX . '/+/+/+/+/+',
		MqttConnectorPlugin\Constants::MQTT_API_PREFIX . MqttConnectorPlugin\Constants::MQTT_API_V1_VERSION_PREFIX . '/+/+/+/+/+/+',
		MqttConnectorPlugin\Constants::MQTT_API_PREFIX . MqttConnectorPlugin\Constants::MQTT_API_V1_VERSION_PREFIX . '/+/+/+/+/+/+/+',

		MqttConnectorPlugin\Constants::MQTT_API_PREFIX . MqttConnectorPlugin\Constants::MQTT_API_V1_VERSION_PREFIX . '/+/$child/+/+',
		MqttConnectorPlugin\Constants::MQTT_API_PREFIX . MqttConnectorPlugin\Constants::MQTT_API_V1_VERSION_PREFIX . '/+/$child/+/+/+',
		MqttConnectorPlugin\Constants::MQTT_API_PREFIX . MqttConnectorPlugin\Constants::MQTT_API_V1_VERSION_PREFIX . '/+/$child/+/+/+/+',
		MqttConnectorPlugin\Constants::MQTT_API_PREFIX . MqttConnectorPlugin\Constants::MQTT_API_V1_VERSION_PREFIX . '/+/$child/+/+/+/+/+',
		MqttConnectorPlugin\Constants::MQTT_API_PREFIX . MqttConnectorPlugin\Constants::MQTT_API_V1_VERSION_PREFIX . '/+/$child/+/+/+/+/+/+',
		MqttConnectorPlugin\Constants::MQTT_API_PREFIX . MqttConnectorPlugin\Constants::MQTT_API_V1_VERSION_PREFIX . '/+/$child/+/+/+/+/+/+/+',
	];

	/** @var Consumers\IConsumer */
	private Consumers\IConsumer $consumer;

	/** @var API\V1Validator */
	private API\V1Validator $validator;

	/** @var API\V1Parser */
	private API\V1Parser $parser;

	/** @var Log\LoggerInterface */
	private Log\LoggerInterface $logger;

	public function __construct(
		Consumers\IConsumer $consumer,
		API\V1Validator $validator,
		API\V1Parser $parser,
		?Log\LoggerInterface $logger = null
	) {
		$this->consumer = $consumer;
		$this->validator = $validator;
		$this->parser = $parser;
		$this->logger = $logger ?? new Log\NullLogger();
	}

	/**
	 * {@inheritDoc}
	 */
	public function onOpen(Mqtt\Connection $connection, Client\MqttClient $client): void
	{
		// TODO: Implement onOpen() method.
	}

	/**
	 * {@inheritDoc}
	 */
	public function onClose(Mqtt\Connection $connection, Client\MqttClient $client): void
	{
		// TODO: Implement onClose() method.
	}

	/**
	 * {@inheritDoc}
	 */
	public function onConnect(Mqtt\Connection $connection, Client\MqttClient $client): void
	{
		// Get all device topics...
		foreach (self::DEVICES_TOPICS as $topic) {
			$topic = new Mqtt\DefaultSubscription($topic);

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
	}

	/**
	 * {@inheritDoc}
	 */
	public function onDisconnect(Mqtt\Connection $connection, Client\MqttClient $client): void
	{
		// TODO: Implement onDisconnect() method.
	}

	/**
	 * {@inheritDoc}
	 */
	public function onWarning(Throwable $ex, Client\MqttClient $client): void
	{
		// TODO: Implement onWarning() method.
	}

	/**
	 * {@inheritDoc}
	 */
	public function onError(Throwable $ex, Client\MqttClient $client): void
	{
		// TODO: Implement onError() method.
	}

	/**
	 * {@inheritDoc}
	 */
	public function onMessage(Mqtt\Message $message, Client\MqttClient $client): void
	{
		// Connected device topic
		if (
			$this->validator->validateConvention($message->getTopic())
			&& $this->validator->validateVersion($message->getTopic())
		) {
			// Check if message is sent from broker
			if (!$this->validator->validate($message->getTopic())) {
				return;
			}

			try {
				$entity = $this->parser->parse(
					Uuid\Uuid::fromString($client->getClientId()),
					$message->getTopic(),
					$message->getPayload(),
					$message->isRetained()
				);

			} catch (Exceptions\ParseMessageException $ex) {
				$this->logger->debug(
					'[FB:PLUGIN:MQTT] Received message could not be successfully parsed to entity.',
					[
						'exception' => [
							'message' => $ex->getMessage(),
							'code'    => $ex->getCode(),
						],
					]
				);

				return;
			}

			$this->consumer->consume($entity);
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
