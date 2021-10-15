<?php declare(strict_types = 1);

/**
 * V1Validator.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:MqttConnectorPlugin!
 * @subpackage     API
 * @since          0.1.0
 *
 * @date           24.02.20
 */

namespace FastyBird\MqttConnectorPlugin\API;

use FastyBird\MqttConnectorPlugin;
use Nette;

/**
 * API v1 topic validator
 *
 * @package        FastyBird:MqttConnectorPlugin!
 * @subpackage     API
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class V1Validator
{

	use Nette\SmartObject;

	// TOPIC: /fb/*
	public const CONVENTION_PREFIX_REGEXP = '/^\/fb\/.*$/';

	// TOPIC: /fb/v1/*
	public const API_VERSION_REGEXP = '/^\/fb\/v1\/.*$/';

	// TOPIC: /fb/v1/<device-name>/$child/<child-device-name>/*
	public const DEVICE_CHILD_PARTIAL_REGEXP = '/^\/fb\/v1\/([a-z0-9-]+)\/\$child\/([a-z0-9-]+)\/(.*)$/';
	// TOPIC: /fb/v1/<device-name>/$channel/<channel-name>/*
	public const CHANNEL_PARTIAL_REGEXP = '/^\/fb\/v1\/([a-z0-9-]+)\/\$channel\/([a-z0-9-]+)\/.*$/';
	// TOPIC: /fb/v1/<device-name>/$child/<child-device-name>/$channel/<channel-name>/*
	public const CHILD_DEVICE_CHANNEL_PARTIAL_REGEXP = '/^\/fb\/v1\/([a-z0-9-]+)\/\$child\/([a-z0-9-]+)\/\$channel\/([a-z0-9-]+)\/.*$/';

	// TOPIC: /fb/v1/<device-name>/<$name|$properties|$control|$channels|$extensions>
	public const DEVICE_ATTRIBUTE_REGEXP = '/^\/fb\/v1\/([a-z0-9-]+)\/\$(state|name|properties|control|channels|extensions)$/';
	// TOPIC: /fb/v1/<device-name>/$child/<child-device-name>/$name|<$properties|$control|$channels|$extensions>
	public const DEVICE_CHILD_ATTRIBUTE_REGEXP = '/^\/fb\/v1\/([a-z0-9-]+)\/\$child\/([a-z0-9-]+)\/\$(state|name|properties|control|channels|extensions)$/';

	// TOPIC: /fb/v1/<device-name>/$hw/<mac-address|manufacturer|model|version>
	public const DEVICE_HW_INFO_REGEXP = '/^\/fb\/v1\/([a-z0-9-]+)\/\$hw\/(mac-address|manufacturer|model|version)$/';
	// TOPIC: /fb/v1/<device-name>/$child/<child-device-name>/$hw/<mac-address|manufacturer|model|version>
	public const DEVICE_CHILD_HW_INFO_REGEXP = '/^\/fb\/v1\/([a-z0-9-]+)\/\$child\/([a-z0-9-]+)\/\$hw\/(mac-address|manufacturer|model|version)$/';
	// TOPIC: /fb/v1/<device-name>/$fw/<manufacturer|name|version>
	public const DEVICE_FW_INFO_REGEXP = '/^\/fb\/v1\/([a-z0-9-]+)\/\$fw\/(manufacturer|name|version)$/';
	// TOPIC: /fb/v1/<device-name>/$child/<child-device-name>/$fw/<manufacturer|name|version>
	public const DEVICE_CHILD_FW_INFO_REGEXP = '/^\/fb\/v1\/([a-z0-9-]+)\/\$child\/([a-z0-9-]+)\/\$fw\/(manufacturer|name|version)$/';

	// TOPIC: /fb/v1/<device-name>/$property/<property-name>[/<$name|$type|$settable|$queryable|$datatype|$format|$unit>]
	public const DEVICE_PROPERTY_REGEXP = '/^\/fb\/v1\/([a-z0-9-]+)\/\$property\/([a-z0-9-]+)((\/\$)(name|type|settable|queryable|datatype|format|unit))?$/';
	// TOPIC: /fb/v1/<device-name>/$child/<child-device-name>/$property/<property-name>[/<$name|$type|$settable|$queryable|$datatype|$format|$unit>]
	public const DEVICE_CHILD_PROPERTY_REGEXP = '/^\/fb\/v1\/([a-z0-9-]+)\/\$child\/([a-z0-9-]+)\/\$property\/([a-z0-9-]+)((\/\$)(name|type|settable|queryable|datatype|format|unit))?$/';

	// TOPIC: /fb/v1/<device-name>/$control/<configure|reset|reconnect|factory-reset|ota>[/$schema]
	public const DEVICE_CONTROL_REGEXP = '/^\/fb\/v1\/([a-z0-9-]+)\/\$control\/(configure|reset|reconnect|factory-reset|ota)((\/\$)(schema))?$/';
	// TOPIC: /fb/v1/<device-name>/$child/<child-device-name>/$control/<configure|reset|reconnect|factory-reset|ota>[/$schema]
	public const DEVICE_CHILD_CONTROL_REGEXP = '/^\/fb\/v1\/([a-z0-9-]+)\/\$child\/([a-z0-9-]+)\/\$control\/(configure|reset|reconnect|factory-reset|ota)((\/\$)(schema))?$/';

	// TOPIC: /fb/v1/*/$channel/<channel-name>/<$name|$properties|$control>
	public const CHANNEL_ATTRIBUTE_REGEXP = '/\/(.*)\/\$channel\/([a-z0-9-]+)\/\$(name|properties|control)$/';
	// TOPIC: /fb/v1/*/$channel/<channel-name>/$property/<property-name>[/<$name|$type|$settable|$queryable|$datatype|$format|$unit>]
	public const CHANNEL_PROPERTY_REGEXP = '/\/(.*)\/\$channel\/([a-z0-9-]+)\/\$property\/([a-z0-9-]+)((\/\$)(name|type|settable|queryable|datatype|format|unit))?$/';
	// TOPIC: /fb/v1/*/$channel/<channel-name>/$control/<configure>[/$schema]
	public const CHANNEL_CONTROL_REGEXP = '/\/(.*)\/\$channel\/([a-z0-9-]+)\/\$control\/(configure)((\/\$)(schema))?$/';

	/**
	 * @param string $topic
	 *
	 * @return bool
	 */
	public function validate(string $topic): bool
	{
		// Check if message is sent from broker
		if (strpos(trim($topic, MqttConnectorPlugin\Constants::MQTT_TOPIC_DELIMITER), MqttConnectorPlugin\Constants::MQTT_TOPIC_DELIMITER . 'set') !== false) {
			return false;
		}

		// Check for valid convention prefix
		if (!$this->validateConvention($topic)) {
			return false;
		}

		// Check for valid version V1
		if (!$this->validateVersion($topic)) {
			return false;
		}

		if ($this->validateDeviceAttribute($topic)) {
			return true;
		}

		if ($this->validateDeviceHardwareInfo($topic)) {
			return true;
		}

		if ($this->validateDeviceFirmwareInfo($topic)) {
			return true;
		}

		if ($this->validateDeviceProperty($topic)) {
			return true;
		}

		if ($this->validateDeviceControl($topic)) {
			return true;
		}

		// Check for channel topics
		if ($this->validateChannelPart($topic)) {
			if ($this->validateChannelAttribute($topic)) {
				return true;
			}

			if ($this->validateChannelProperty($topic)) {
				return true;
			}

			if ($this->validateChannelControl($topic)) {
				return true;
			}
		}

		return false;
	}

	/**
	 * @param string $topic
	 *
	 * @return bool
	 */
	public function validateConvention(string $topic): bool
	{
		return preg_match(self::CONVENTION_PREFIX_REGEXP, $topic) === 1;
	}

	/**
	 * @param string $topic
	 *
	 * @return bool
	 */
	public function validateVersion(string $topic): bool
	{
		return preg_match(self::API_VERSION_REGEXP, $topic) === 1;
	}

	/**
	 * @param string $topic
	 *
	 * @return bool
	 */
	public function validateDeviceAttribute(string $topic): bool
	{
		if (preg_match(self::DEVICE_ATTRIBUTE_REGEXP, $topic) === 1) {
			return true;
		}

		return $this->validateChildDevicePart($topic) && preg_match(self::DEVICE_CHILD_ATTRIBUTE_REGEXP, $topic) === 1;
	}

	/**
	 * @param string $topic
	 *
	 * @return bool
	 */
	public function validateChildDevicePart(string $topic): bool
	{
		return preg_match(self::DEVICE_CHILD_PARTIAL_REGEXP, $topic) === 1;
	}

	/**
	 * @param string $topic
	 *
	 * @return bool
	 */
	public function validateDeviceHardwareInfo(string $topic): bool
	{
		if (preg_match(self::DEVICE_HW_INFO_REGEXP, $topic) === 1) {
			return true;
		}

		return $this->validateChildDevicePart($topic) && preg_match(self::DEVICE_CHILD_HW_INFO_REGEXP, $topic) === 1;
	}

	/**
	 * @param string $topic
	 *
	 * @return bool
	 */
	public function validateDeviceFirmwareInfo(string $topic): bool
	{
		if (preg_match(self::DEVICE_FW_INFO_REGEXP, $topic) === 1) {
			return true;
		}

		return $this->validateChildDevicePart($topic) && preg_match(self::DEVICE_CHILD_FW_INFO_REGEXP, $topic) === 1;
	}

	/**
	 * @param string $topic
	 *
	 * @return bool
	 */
	public function validateDeviceProperty(string $topic): bool
	{
		if (preg_match(self::DEVICE_PROPERTY_REGEXP, $topic) === 1) {
			return true;
		}

		return $this->validateChildDevicePart($topic) && preg_match(self::DEVICE_CHILD_PROPERTY_REGEXP, $topic) === 1;
	}

	/**
	 * @param string $topic
	 *
	 * @return bool
	 */
	public function validateDeviceControl(string $topic): bool
	{
		if (preg_match(self::DEVICE_CONTROL_REGEXP, $topic) === 1) {
			return true;
		}

		return $this->validateChildDevicePart($topic) && preg_match(self::DEVICE_CHILD_CONTROL_REGEXP, $topic) === 1;
	}

	/**
	 * @param string $topic
	 *
	 * @return bool
	 */
	public function validateChannelPart(string $topic): bool
	{
		if (preg_match(self::CHANNEL_PARTIAL_REGEXP, $topic) === 1) {
			return true;
		}

		return $this->validateChildDevicePart($topic) && preg_match(self::CHILD_DEVICE_CHANNEL_PARTIAL_REGEXP, $topic) === 1;
	}

	/**
	 * @param string $topic
	 *
	 * @return bool
	 */
	public function validateChannelAttribute(string $topic): bool
	{
		return $this->validateChannelPart($topic) && preg_match(self::CHANNEL_ATTRIBUTE_REGEXP, $topic) === 1;
	}

	/**
	 * @param string $topic
	 *
	 * @return bool
	 */
	public function validateChannelProperty(string $topic): bool
	{
		return $this->validateChannelPart($topic) && preg_match(self::CHANNEL_PROPERTY_REGEXP, $topic) === 1;
	}

	/**
	 * @param string $topic
	 *
	 * @return bool
	 */
	public function validateChannelControl(string $topic): bool
	{
		return $this->validateChannelPart($topic) && preg_match(self::CHANNEL_CONTROL_REGEXP, $topic) === 1;
	}

}
