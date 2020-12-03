<?php declare(strict_types = 1);

/**
 * MqttClientV1ConnectHandler.php
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
use FastyBird\MqttPlugin;
use IPub\MQTTClient;
use Nette;
use Psr\Log;
use Throwable;

/**
 * MQTT client connected event handler
 *
 * @package         FastyBird:MqttPlugin!
 * @subpackage      Events
 *
 * @author          Adam Kadlec <adam.kadlec@fastybird.com>
 */
class MqttClientV1ConnectHandler
{

	use Nette\SmartObject;

	// MQTT api topics subscribe format
	public const DEVICES_TOPICS = [
		MqttPlugin\Constants::MQTT_API_PREFIX . MqttPlugin\Constants::MQTT_API_V1_VERSION_PREFIX . '/+/+',
		MqttPlugin\Constants::MQTT_API_PREFIX . MqttPlugin\Constants::MQTT_API_V1_VERSION_PREFIX . '/+/+/+',
		MqttPlugin\Constants::MQTT_API_PREFIX . MqttPlugin\Constants::MQTT_API_V1_VERSION_PREFIX . '/+/+/+/+',
		MqttPlugin\Constants::MQTT_API_PREFIX . MqttPlugin\Constants::MQTT_API_V1_VERSION_PREFIX . '/+/+/+/+/+',
		MqttPlugin\Constants::MQTT_API_PREFIX . MqttPlugin\Constants::MQTT_API_V1_VERSION_PREFIX . '/+/+/+/+/+/+',
		MqttPlugin\Constants::MQTT_API_PREFIX . MqttPlugin\Constants::MQTT_API_V1_VERSION_PREFIX . '/+/+/+/+/+/+/+',

		MqttPlugin\Constants::MQTT_API_PREFIX . MqttPlugin\Constants::MQTT_API_V1_VERSION_PREFIX . '/+/$child/+/+',
		MqttPlugin\Constants::MQTT_API_PREFIX . MqttPlugin\Constants::MQTT_API_V1_VERSION_PREFIX . '/+/$child/+/+/+',
		MqttPlugin\Constants::MQTT_API_PREFIX . MqttPlugin\Constants::MQTT_API_V1_VERSION_PREFIX . '/+/$child/+/+/+/+',
		MqttPlugin\Constants::MQTT_API_PREFIX . MqttPlugin\Constants::MQTT_API_V1_VERSION_PREFIX . '/+/$child/+/+/+/+/+',
		MqttPlugin\Constants::MQTT_API_PREFIX . MqttPlugin\Constants::MQTT_API_V1_VERSION_PREFIX . '/+/$child/+/+/+/+/+/+',
		MqttPlugin\Constants::MQTT_API_PREFIX . MqttPlugin\Constants::MQTT_API_V1_VERSION_PREFIX . '/+/$child/+/+/+/+/+/+/+',
	];

	/** @var Log\LoggerInterface */
	private $logger;

	public function __construct(
		?Log\LoggerInterface $logger = null
	) {
		$this->logger = $logger ?? new Log\NullLogger();
	}

	/**
	 * @param Mqtt\Connection $connection
	 * @param MQTTClient\Client\IClient $client
	 *
	 * @return void
	 */
	public function __invoke(Mqtt\Connection $connection, MQTTClient\Client\IClient $client): void
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

}
