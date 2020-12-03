<?php declare(strict_types = 1);

/**
 * MqttClientWarningHandler.php
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

use IPub\MQTTClient;
use Nette;
use Psr\Log;
use Throwable;

/**
 * MQTT client warning event handler
 *
 * @package         FastyBird:MqttPlugin!
 * @subpackage      Events
 *
 * @author          Adam Kadlec <adam.kadlec@fastybird.com>
 */
class MqttClientWarningHandler
{

	use Nette\SmartObject;

	/** @var Log\LoggerInterface */
	private $logger;

	public function __construct(
		?Log\LoggerInterface $logger = null
	) {
		$this->logger = $logger ?? new Log\NullLogger();
	}

	/**
	 * @param Throwable $ex
	 * @param MQTTClient\Client\IClient $client
	 *
	 * @return void
	 */
	public function __invoke(Throwable $ex, MQTTClient\Client\IClient $client): void
	{
		// Broker warning occur
		$this->logger->warning(sprintf('[MQTT_CLIENT] There was an error  %s', $ex->getMessage()), [
			'server' => [
				'uri'  => $client->getUri(),
				'port' => $client->getPort(),
			],
			'error'  => [
				'message' => $ex->getMessage(),
				'code'    => $ex->getCode(),
			],
		]);

		$client->getLoop()->stop();
	}

}
