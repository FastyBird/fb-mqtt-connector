<?php declare(strict_types = 1);

/**
 * MqttClientV1MessageHandler.php
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
use FastyBird\MqttPlugin\API;
use FastyBird\MqttPlugin\Consumers;
use FastyBird\MqttPlugin\Exceptions;
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
class MqttClientV1MessageHandler
{

	use Nette\SmartObject;

	/** @var Consumers\ExchangeConsumer */
	private Consumers\ExchangeConsumer $consumer;

	/** @var API\V1Validator */
	private API\V1Validator $validator;

	/** @var API\V1Parser */
	private API\V1Parser $parser;

	/** @var Log\LoggerInterface */
	private Log\LoggerInterface $logger;

	public function __construct(
		Consumers\ExchangeConsumer $consumer,
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
	 * @param Mqtt\Message $message
	 * @param MQTTClient\Client\IClient $client
	 *
	 * @return void
	 */
	public function __invoke(Mqtt\Message $message, MQTTClient\Client\IClient $client): void
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
				$entity = $this->parser->parse($message->getTopic(), $message->getPayload(), $message->isRetained());

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

}
