<?php declare(strict_types = 1);

/**
 * ApiV1Publisher.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:MqttConnectorPlugin!
 * @subpackage     Publishers
 * @since          0.1.0
 *
 * @date           24.02.20
 */

namespace FastyBird\MqttConnectorPlugin\Publishers;

use FastyBird\MqttConnectorPlugin;
use FastyBird\MqttConnectorPlugin\Client;
use FastyBird\MqttConnectorPlugin\Entities;
use FastyBird\MqttConnectorPlugin\Exceptions;
use Nette;
use Nette\Utils;
use Psr\Log;

/**
 * MQTT api V1 publisher
 *
 * @package        FastyBird:MqttConnectorPlugin!
 * @subpackage     Publishers
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class ApiV1Publisher implements IPublisher
{

	use Nette\SmartObject;

	/**
	 * Exchange topics
	 */
	private const DEVICE_PROPERTY_TOPIC
		= MqttConnectorPlugin\Constants::MQTT_API_PREFIX
		. MqttConnectorPlugin\Constants::MQTT_API_V1_VERSION_PREFIX
		. MqttConnectorPlugin\Constants::MQTT_TOPIC_DELIMITER
		. self::DEVICE_REPLACE_STRING
		. MqttConnectorPlugin\Constants::MQTT_TOPIC_DELIMITER
		. '$property'
		. MqttConnectorPlugin\Constants::MQTT_TOPIC_DELIMITER
		. self::PROPERTY_REPLACE_STRING
		. MqttConnectorPlugin\Constants::MQTT_TOPIC_DELIMITER
		. 'set';

	private const DEVICE_CHILD_PROPERTY_TOPIC
		= MqttConnectorPlugin\Constants::MQTT_API_PREFIX
		. MqttConnectorPlugin\Constants::MQTT_API_V1_VERSION_PREFIX
		. MqttConnectorPlugin\Constants::MQTT_TOPIC_DELIMITER
		. self::PARENT_REPLACE_STRING
		. MqttConnectorPlugin\Constants::MQTT_TOPIC_DELIMITER
		. '$child'
		. MqttConnectorPlugin\Constants::MQTT_TOPIC_DELIMITER
		. self::DEVICE_REPLACE_STRING
		. MqttConnectorPlugin\Constants::MQTT_TOPIC_DELIMITER
		. '$property'
		. MqttConnectorPlugin\Constants::MQTT_TOPIC_DELIMITER
		. self::PROPERTY_REPLACE_STRING
		. MqttConnectorPlugin\Constants::MQTT_TOPIC_DELIMITER
		. 'set';

	private const DEVICE_CONTROL_SET_TOPIC
		= MqttConnectorPlugin\Constants::MQTT_API_PREFIX
		. MqttConnectorPlugin\Constants::MQTT_API_V1_VERSION_PREFIX
		. MqttConnectorPlugin\Constants::MQTT_TOPIC_DELIMITER
		. self::DEVICE_REPLACE_STRING
		. MqttConnectorPlugin\Constants::MQTT_TOPIC_DELIMITER
		. '$control'
		. MqttConnectorPlugin\Constants::MQTT_TOPIC_DELIMITER
		. self::CONTROL_REPLACE_STRING
		. MqttConnectorPlugin\Constants::MQTT_TOPIC_DELIMITER
		. 'set';

	private const DEVICE_CHILD_CONTROL_SET_TOPIC
		= MqttConnectorPlugin\Constants::MQTT_API_PREFIX
		. MqttConnectorPlugin\Constants::MQTT_API_V1_VERSION_PREFIX
		. MqttConnectorPlugin\Constants::MQTT_TOPIC_DELIMITER
		. self::PARENT_REPLACE_STRING
		. MqttConnectorPlugin\Constants::MQTT_TOPIC_DELIMITER
		. '$child'
		. MqttConnectorPlugin\Constants::MQTT_TOPIC_DELIMITER
		. self::DEVICE_REPLACE_STRING
		. MqttConnectorPlugin\Constants::MQTT_TOPIC_DELIMITER
		. '$control'
		. MqttConnectorPlugin\Constants::MQTT_TOPIC_DELIMITER
		. self::CONTROL_REPLACE_STRING
		. MqttConnectorPlugin\Constants::MQTT_TOPIC_DELIMITER
		. 'set';

	private const CHANNEL_PROPERTY_TOPIC
		= MqttConnectorPlugin\Constants::MQTT_API_PREFIX
		. MqttConnectorPlugin\Constants::MQTT_API_V1_VERSION_PREFIX
		. MqttConnectorPlugin\Constants::MQTT_TOPIC_DELIMITER
		. self::DEVICE_REPLACE_STRING
		. MqttConnectorPlugin\Constants::MQTT_TOPIC_DELIMITER
		. '$channel'
		. MqttConnectorPlugin\Constants::MQTT_TOPIC_DELIMITER
		. self::CHANNEL_REPLACE_STRING
		. MqttConnectorPlugin\Constants::MQTT_TOPIC_DELIMITER
		. '$property'
		. MqttConnectorPlugin\Constants::MQTT_TOPIC_DELIMITER
		. self::PROPERTY_REPLACE_STRING
		. MqttConnectorPlugin\Constants::MQTT_TOPIC_DELIMITER
		. 'set';

	private const CHANNEL_CONFIGURE_TOPIC
		= MqttConnectorPlugin\Constants::MQTT_API_PREFIX
		. MqttConnectorPlugin\Constants::MQTT_API_V1_VERSION_PREFIX
		. MqttConnectorPlugin\Constants::MQTT_TOPIC_DELIMITER
		. self::DEVICE_REPLACE_STRING
		. MqttConnectorPlugin\Constants::MQTT_TOPIC_DELIMITER
		. '$channel'
		. MqttConnectorPlugin\Constants::MQTT_TOPIC_DELIMITER
		. self::CHANNEL_REPLACE_STRING
		. MqttConnectorPlugin\Constants::MQTT_TOPIC_DELIMITER
		. '$control'
		. MqttConnectorPlugin\Constants::MQTT_TOPIC_DELIMITER
		. self::CONTROL_REPLACE_STRING
		. MqttConnectorPlugin\Constants::MQTT_TOPIC_DELIMITER
		. 'set';

	private const CHANNEL_CHILD_PROPERTY_TOPIC
		= MqttConnectorPlugin\Constants::MQTT_API_PREFIX
		. MqttConnectorPlugin\Constants::MQTT_API_V1_VERSION_PREFIX
		. MqttConnectorPlugin\Constants::MQTT_TOPIC_DELIMITER
		. self::PARENT_REPLACE_STRING
		. MqttConnectorPlugin\Constants::MQTT_TOPIC_DELIMITER
		. '$child'
		. MqttConnectorPlugin\Constants::MQTT_TOPIC_DELIMITER
		. self::DEVICE_REPLACE_STRING
		. MqttConnectorPlugin\Constants::MQTT_TOPIC_DELIMITER
		. '$channel'
		. MqttConnectorPlugin\Constants::MQTT_TOPIC_DELIMITER
		. self::CHANNEL_REPLACE_STRING
		. MqttConnectorPlugin\Constants::MQTT_TOPIC_DELIMITER
		. '$property'
		. MqttConnectorPlugin\Constants::MQTT_TOPIC_DELIMITER
		. self::PROPERTY_REPLACE_STRING
		. MqttConnectorPlugin\Constants::MQTT_TOPIC_DELIMITER
		. 'set';

	private const CHANNEL_CHILD_CONFIGURE_TOPIC
		= MqttConnectorPlugin\Constants::MQTT_API_PREFIX
		. MqttConnectorPlugin\Constants::MQTT_API_V1_VERSION_PREFIX
		. MqttConnectorPlugin\Constants::MQTT_TOPIC_DELIMITER
		. self::PARENT_REPLACE_STRING
		. MqttConnectorPlugin\Constants::MQTT_TOPIC_DELIMITER
		. '$child'
		. MqttConnectorPlugin\Constants::MQTT_TOPIC_DELIMITER
		. self::DEVICE_REPLACE_STRING
		. MqttConnectorPlugin\Constants::MQTT_TOPIC_DELIMITER
		. '$channel'
		. MqttConnectorPlugin\Constants::MQTT_TOPIC_DELIMITER
		. self::CHANNEL_REPLACE_STRING
		. MqttConnectorPlugin\Constants::MQTT_TOPIC_DELIMITER
		. '$control'
		. MqttConnectorPlugin\Constants::MQTT_TOPIC_DELIMITER
		. self::CONTROL_REPLACE_STRING
		. MqttConnectorPlugin\Constants::MQTT_TOPIC_DELIMITER
		. 'set';

	/** @var Client\Client */
	private Client\Client $client;

	/** @var Log\LoggerInterface */
	private Log\LoggerInterface $logger;

	public function __construct(
		Client\Client $client,
		?Log\LoggerInterface $logger = null
	) {
		$this->client = $client;
		$this->logger = $logger ?? new Log\NullLogger();
	}

	/**
	 * {@inheritDoc}
	 */
	public function sendDeviceProperty(
		string $device,
		string $property,
		string $payload,
		?string $parentDevice = null
	): void {
		$this->sendToDevice(
			$this->buildDevicePropertyTopic(
				$parentDevice !== null ? self::DEVICE_CHILD_PROPERTY_TOPIC : self::DEVICE_PROPERTY_TOPIC,
				$device,
				$property,
				$parentDevice
			),
			$payload
		);
	}

	/**
	 * {@inheritDoc}
	 */
	public function sendDeviceConfiguration(
		string $device,
		Utils\ArrayHash $configuration,
		?string $parentDevice = null
	): void {
		try {
			$this->sendToDevice(
				$this->buildDeviceControlTopic(
					$parentDevice !== null ? self::DEVICE_CHILD_CONTROL_SET_TOPIC : self::DEVICE_CONTROL_SET_TOPIC,
					$device,
					Entities\DeviceControl::CONFIG,
					$parentDevice
				),
				Utils\Json::encode((array) $configuration)
			);

		} catch (Utils\JsonException $ex) {
			throw new Exceptions\InvalidArgumentException('Provided payload could not be json-encoded');
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function sendChannelProperty(
		string $device,
		string $channel,
		string $property,
		string $payload,
		?string $parentDevice = null
	): void {
		$this->sendToDevice(
			$this->buildChannelPropertyTopic(
				$parentDevice !== null ? self::CHANNEL_CHILD_PROPERTY_TOPIC : self::CHANNEL_PROPERTY_TOPIC,
				$device,
				$channel,
				$property,
				$parentDevice
			),
			$payload
		);
	}

	/**
	 * {@inheritDoc}
	 */
	public function sendChannelConfiguration(
		string $device,
		string $channel,
		Utils\ArrayHash $configuration,
		?string $parentDevice = null
	): void {
		try {
			$this->sendToDevice(
				$this->buildChannelControlTopic(
					$parentDevice !== null ? self::CHANNEL_CHILD_CONFIGURE_TOPIC : self::CHANNEL_CONFIGURE_TOPIC,
					$device,
					$channel,
					$parentDevice
				),
				Utils\Json::encode((array) $configuration)
			);

		} catch (Utils\JsonException $ex) {
			throw new Exceptions\InvalidArgumentException('Provided payload could not be json-encoded');
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function sendDeviceRestart(
		string $device,
		?string $parentDevice = null
	): void {
		$this->sendToDevice(
			$this->buildDeviceControlTopic(
				$parentDevice !== null ? self::DEVICE_CHILD_CONTROL_SET_TOPIC : self::DEVICE_CONTROL_SET_TOPIC,
				$device,
				Entities\DeviceControl::RESET,
				$parentDevice
			),
			'true'
		);
	}

	/**
	 * {@inheritDoc}
	 */
	public function sendDeviceReconnect(
		string $device,
		?string $parentDevice = null
	): void {
		$this->sendToDevice(
			$this->buildDeviceControlTopic(
				$parentDevice !== null ? self::DEVICE_CHILD_CONTROL_SET_TOPIC : self::DEVICE_CONTROL_SET_TOPIC,
				$device,
				Entities\DeviceControl::RECONNECT,
				$parentDevice
			),
			'true'
		);
	}

	/**
	 * {@inheritDoc}
	 */
	public function sendDeviceFactoryReset(
		string $device,
		?string $parentDevice = null
	): void {
		$this->sendToDevice(
			$this->buildDeviceControlTopic(
				$parentDevice !== null ? self::DEVICE_CHILD_CONTROL_SET_TOPIC : self::DEVICE_CONTROL_SET_TOPIC,
				$device,
				Entities\DeviceControl::FACTORY_RESET,
				$parentDevice
			),
			'true'
		);
	}

	/**
	 * @param string $topic
	 * @param string|null $payload
	 * @param int $qos
	 * @param bool $retained
	 *
	 * @return void
	 */
	private function sendToDevice(
		string $topic,
		?string $payload,
		int $qos = MqttConnectorPlugin\Constants::MQTT_API_QOS_1,
		bool $retained = false
	): void {
		$this->logger->info(
			sprintf('[FB:PLUGIN:MQTT] Published message to topic: %s', $topic),
			[
				'topic'   => $topic,
				'payload' => $payload,
				'qos'     => $qos,
			]
		);

		$this->client->publish($topic, $payload, $qos, $retained);
	}

	/**
	 * @param string $topic
	 * @param string $device
	 * @param string $property
	 * @param string|null $parentDevice
	 *
	 * @return string
	 */
	private function buildDevicePropertyTopic(
		string $topic,
		string $device,
		string $property,
		?string $parentDevice = null
	): string {
		if ($parentDevice !== null) {
			$topic = str_replace(self::PARENT_REPLACE_STRING, $parentDevice, $topic);
		}

		$topic = str_replace(self::DEVICE_REPLACE_STRING, $device, $topic);
		$topic = str_replace(self::PROPERTY_REPLACE_STRING, $property, $topic);

		return $topic;
	}

	/**
	 * @param string $topic
	 * @param string $device
	 * @param string $control
	 * @param string|null $parentDevice
	 *
	 * @return string
	 */
	private function buildDeviceControlTopic(
		string $topic,
		string $device,
		string $control,
		?string $parentDevice = null
	): string {
		if ($parentDevice !== null) {
			$topic = str_replace(self::PARENT_REPLACE_STRING, $parentDevice, $topic);
		}

		$topic = str_replace(self::DEVICE_REPLACE_STRING, $device, $topic);
		$topic = str_replace(self::CONTROL_REPLACE_STRING, $control, $topic);

		return $topic;
	}

	/**
	 * @param string $topic
	 * @param string $device
	 * @param string $channel
	 * @param string $property
	 * @param string|null $parentDevice
	 *
	 * @return string
	 */
	private function buildChannelPropertyTopic(
		string $topic,
		string $device,
		string $channel,
		string $property,
		?string $parentDevice = null
	): string {
		if ($parentDevice !== null) {
			$topic = str_replace(self::PARENT_REPLACE_STRING, $parentDevice, $topic);
		}

		$topic = str_replace(self::DEVICE_REPLACE_STRING, $device, $topic);
		$topic = str_replace(self::CHANNEL_REPLACE_STRING, $channel, $topic);
		$topic = str_replace(self::PROPERTY_REPLACE_STRING, $property, $topic);

		return $topic;
	}

	/**
	 * @param string $topic
	 * @param string $device
	 * @param string $channel
	 * @param string|null $parentDevice
	 *
	 * @return string
	 */
	private function buildChannelControlTopic(
		string $topic,
		string $device,
		string $channel,
		?string $parentDevice = null
	): string {
		if ($parentDevice !== null) {
			$topic = str_replace(self::PARENT_REPLACE_STRING, $parentDevice, $topic);
		}

		$topic = str_replace(self::DEVICE_REPLACE_STRING, $device, $topic);
		$topic = str_replace(self::CHANNEL_REPLACE_STRING, $channel, $topic);
		$topic = str_replace(self::CONTROL_REPLACE_STRING, Entities\ChannelControl::CONFIG, $topic);

		return $topic;
	}

}
