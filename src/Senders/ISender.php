<?php declare(strict_types = 1);

/**
 * ISender.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:MqttPlugin!
 * @subpackage     Senders
 * @since          0.1.0
 *
 * @date           24.02.20
 */

namespace FastyBird\MqttPlugin\Senders;

use Nette\Utils;
use React\Promise;

/**
 * MQTT api sender interface
 *
 * @package        FastyBird:MqttPlugin!
 * @subpackage     Senders
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface ISender
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
	 * @return string
	 */
	public function getVersion(): string;

	/**
	 * @param string $device
	 * @param string $channel
	 * @param string $property
	 * @param string $payload
	 * @param string|null $parentDevice
	 *
	 * @return Promise\ExtendedPromiseInterface
	 */
	public function sendChannelProperty(
		string $device,
		string $channel,
		string $property,
		string $payload,
		?string $parentDevice = null
	): Promise\ExtendedPromiseInterface;

	/**
	 * @param string $device
	 * @param string $channel
	 * @param Utils\ArrayHash $configuration
	 * @param string|null $parentDevice
	 *
	 * @return Promise\ExtendedPromiseInterface
	 */
	public function sendChannelConfiguration(
		string $device,
		string $channel,
		Utils\ArrayHash $configuration,
		?string $parentDevice = null
	): Promise\ExtendedPromiseInterface;

	/**
	 * @param string $device
	 * @param string $property
	 * @param string $payload
	 * @param string|null $parentDevice
	 *
	 * @return Promise\ExtendedPromiseInterface
	 */
	public function sendDeviceProperty(
		string $device,
		string $property,
		string $payload,
		?string $parentDevice = null
	): Promise\ExtendedPromiseInterface;

	/**
	 * @param string $device
	 * @param Utils\ArrayHash $configuration
	 * @param string|null $parentDevice
	 *
	 * @return Promise\ExtendedPromiseInterface
	 */
	public function sendDeviceConfiguration(
		string $device,
		Utils\ArrayHash $configuration,
		?string $parentDevice = null
	): Promise\ExtendedPromiseInterface;

	/**
	 * @param string $device
	 * @param string|null $parentDevice
	 *
	 * @return Promise\ExtendedPromiseInterface
	 */
	public function sendDeviceRestart(
		string $device,
		?string $parentDevice = null
	): Promise\ExtendedPromiseInterface;

	/**
	 * @param string $device
	 * @param string|null $parentDevice
	 *
	 * @return Promise\ExtendedPromiseInterface
	 */
	public function sendDeviceReconnect(
		string $device,
		?string $parentDevice = null
	): Promise\ExtendedPromiseInterface;

	/**
	 * @param string $device
	 * @param string|null $parentDevice
	 *
	 * @return Promise\ExtendedPromiseInterface
	 */
	public function sendDeviceFactoryReset(
		string $device,
		?string $parentDevice = null
	): Promise\ExtendedPromiseInterface;

}
