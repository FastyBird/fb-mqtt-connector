<?php declare(strict_types = 1);

/**
 * V1Builder.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:FbMqttConnector!
 * @subpackage     API
 * @since          0.1.0
 *
 * @date           05.02.22
 */

namespace FastyBird\FbMqttConnector\API;

use FastyBird\FbMqttConnector;
use Nette;

/**
 * API v1 topic builder
 *
 * @package        FastyBird:FbMqttConnector!
 * @subpackage     API
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class V1Builder
{

	use Nette\SmartObject;

	/**
	 * Replace placeholders
	 */
	private const DEVICE_REPLACE_STRING = '{DEVICE_ID}';
	private const CHANNEL_REPLACE_STRING = '{CHANNEL_ID}';
	private const PROPERTY_REPLACE_STRING = '{PROPERTY_ID}';
	private const CONTROL_REPLACE_STRING = '{CONTROL}';

	/**
	 * Exchange topics
	 */
	private const DEVICE_PROPERTY_TOPIC
		= FbMqttConnector\Constants::MQTT_API_PREFIX
		. FbMqttConnector\Constants::MQTT_API_V1_VERSION_PREFIX
		. FbMqttConnector\Constants::MQTT_TOPIC_DELIMITER
		. self::DEVICE_REPLACE_STRING
		. FbMqttConnector\Constants::MQTT_TOPIC_DELIMITER
		. '$property'
		. FbMqttConnector\Constants::MQTT_TOPIC_DELIMITER
		. self::PROPERTY_REPLACE_STRING
		. FbMqttConnector\Constants::MQTT_TOPIC_DELIMITER
		. 'set';

	private const DEVICE_CONTROL_SET_TOPIC
		= FbMqttConnector\Constants::MQTT_API_PREFIX
		. FbMqttConnector\Constants::MQTT_API_V1_VERSION_PREFIX
		. FbMqttConnector\Constants::MQTT_TOPIC_DELIMITER
		. self::DEVICE_REPLACE_STRING
		. FbMqttConnector\Constants::MQTT_TOPIC_DELIMITER
		. '$control'
		. FbMqttConnector\Constants::MQTT_TOPIC_DELIMITER
		. self::CONTROL_REPLACE_STRING
		. FbMqttConnector\Constants::MQTT_TOPIC_DELIMITER
		. 'set';

	private const CHANNEL_PROPERTY_TOPIC
		= FbMqttConnector\Constants::MQTT_API_PREFIX
		. FbMqttConnector\Constants::MQTT_API_V1_VERSION_PREFIX
		. FbMqttConnector\Constants::MQTT_TOPIC_DELIMITER
		. self::DEVICE_REPLACE_STRING
		. FbMqttConnector\Constants::MQTT_TOPIC_DELIMITER
		. '$channel'
		. FbMqttConnector\Constants::MQTT_TOPIC_DELIMITER
		. self::CHANNEL_REPLACE_STRING
		. FbMqttConnector\Constants::MQTT_TOPIC_DELIMITER
		. '$property'
		. FbMqttConnector\Constants::MQTT_TOPIC_DELIMITER
		. self::PROPERTY_REPLACE_STRING
		. FbMqttConnector\Constants::MQTT_TOPIC_DELIMITER
		. 'set';

	private const CHANNEL_CONTROL_SET_TOPIC
		= FbMqttConnector\Constants::MQTT_API_PREFIX
		. FbMqttConnector\Constants::MQTT_API_V1_VERSION_PREFIX
		. FbMqttConnector\Constants::MQTT_TOPIC_DELIMITER
		. self::DEVICE_REPLACE_STRING
		. FbMqttConnector\Constants::MQTT_TOPIC_DELIMITER
		. '$channel'
		. FbMqttConnector\Constants::MQTT_TOPIC_DELIMITER
		. self::CHANNEL_REPLACE_STRING
		. FbMqttConnector\Constants::MQTT_TOPIC_DELIMITER
		. '$control'
		. FbMqttConnector\Constants::MQTT_TOPIC_DELIMITER
		. self::CONTROL_REPLACE_STRING
		. FbMqttConnector\Constants::MQTT_TOPIC_DELIMITER
		. 'set';

	/**
	 * @param string $device
	 * @param string $property
	 *
	 * @return string
	 */
	public function buildDevicePropertyTopic(
		string $device,
		string $property
	): string {
		$topic = self::DEVICE_PROPERTY_TOPIC;
		$topic = str_replace(self::DEVICE_REPLACE_STRING, $device, $topic);
		return str_replace(self::PROPERTY_REPLACE_STRING, $property, $topic);
	}

	/**
	 * @param string $device
	 * @param string $command
	 *
	 * @return string
	 */
	public function buildDeviceCommandTopic(
		string $device,
		string $command
	): string {
		$topic = self::DEVICE_CONTROL_SET_TOPIC;
		$topic = str_replace(self::DEVICE_REPLACE_STRING, $device, $topic);
		return str_replace(self::CONTROL_REPLACE_STRING, $command, $topic);
	}

	/**
	 * @param string $device
	 * @param string $channel
	 * @param string $property
	 *
	 * @return string
	 */
	public function buildChannelPropertyTopic(
		string $device,
		string $channel,
		string $property
	): string {
		$topic = self::CHANNEL_PROPERTY_TOPIC;
		$topic = str_replace(self::DEVICE_REPLACE_STRING, $device, $topic);
		$topic = str_replace(self::CHANNEL_REPLACE_STRING, $channel, $topic);
		return str_replace(self::PROPERTY_REPLACE_STRING, $property, $topic);
	}

	/**
	 * @param string $device
	 * @param string $channel
	 * @param string $command
	 *
	 * @return string
	 */
	public function buildChannelCommandTopic(
		string $device,
		string $channel,
		string $command
	): string {
		$topic = self::CHANNEL_CONTROL_SET_TOPIC;
		$topic = str_replace(self::DEVICE_REPLACE_STRING, $device, $topic);
		$topic = str_replace(self::CHANNEL_REPLACE_STRING, $channel, $topic);
		return str_replace(self::CONTROL_REPLACE_STRING, $command, $topic);
	}

}
