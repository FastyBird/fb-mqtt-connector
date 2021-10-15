<?php declare(strict_types = 1);

/**
 * IPublisher.php
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

use Nette\Utils;

/**
 * MQTT client publisher interface
 *
 * @package        FastyBird:MqttConnectorPlugin!
 * @subpackage     Publishers
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface IPublisher
{

	/**
	 * Replace placeholders
	 */
	public const DEVICE_REPLACE_STRING = '{DEVICE_ID}';
	public const PARENT_REPLACE_STRING = '{PARENT_ID}';
	public const CHANNEL_REPLACE_STRING = '{CHANNEL_ID}';
	public const PROPERTY_REPLACE_STRING = '{PROPERTY_ID}';
	public const CONTROL_REPLACE_STRING = '{CONTROL}';

	/**
	 * @param string $device
	 * @param string $property
	 * @param string $payload
	 * @param string|null $parentDevice
	 *
	 * @return void
	 */
	public function sendDeviceProperty(
		string $device,
		string $property,
		string $payload,
		?string $parentDevice = null
	): void;

	/**
	 * @param string $device
	 * @param Utils\ArrayHash $configuration
	 * @param string|null $parentDevice
	 *
	 * @return void
	 */
	public function sendDeviceConfiguration(
		string $device,
		Utils\ArrayHash $configuration,
		?string $parentDevice = null
	): void;

	/**
	 * @param string $device
	 * @param string $channel
	 * @param string $property
	 * @param string $payload
	 * @param string|null $parentDevice
	 *
	 * @return void
	 */
	public function sendChannelProperty(
		string $device,
		string $channel,
		string $property,
		string $payload,
		?string $parentDevice = null
	): void;

	/**
	 * @param string $device
	 * @param string $channel
	 * @param Utils\ArrayHash $configuration
	 * @param string|null $parentDevice
	 *
	 * @return void
	 */
	public function sendChannelConfiguration(
		string $device,
		string $channel,
		Utils\ArrayHash $configuration,
		?string $parentDevice = null
	): void;

	/**
	 * @param string $device
	 * @param string|null $parentDevice
	 *
	 * @return void
	 */
	public function sendDeviceRestart(
		string $device,
		?string $parentDevice = null
	): void;

	/**
	 * @param string $device
	 * @param string|null $parentDevice
	 *
	 * @return void
	 */
	public function sendDeviceReconnect(
		string $device,
		?string $parentDevice = null
	): void;

	/**
	 * @param string $device
	 * @param string|null $parentDevice
	 *
	 * @return void
	 */
	public function sendDeviceFactoryReset(
		string $device,
		?string $parentDevice = null
	): void;

}
