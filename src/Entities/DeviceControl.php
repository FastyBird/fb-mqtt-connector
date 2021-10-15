<?php declare(strict_types = 1);

/**
 * Control.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:MqttConnectorPlugin!
 * @subpackage     Entities
 * @since          0.1.0
 *
 * @date           05.03.20
 */

namespace FastyBird\MqttConnectorPlugin\Entities;

/**
 * Channel control attribute
 *
 * @package        FastyBird:MqttConnectorPlugin!
 * @subpackage     Entities
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
class DeviceControl extends Control
{

	public const ALLOWED_CONTROLS = [
		self::CONFIG,
		self::RESET,
		self::RECONNECT,
		self::FACTORY_RESET,
		self::OTA,
	];

	/**
	 * {@inheritDoc}
	 */
	protected function getAllowedControls(): array
	{
		return self::ALLOWED_CONTROLS;
	}

}
