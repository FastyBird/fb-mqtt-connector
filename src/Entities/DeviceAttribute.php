<?php declare(strict_types = 1);

/**
 * DeviceAttribute.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:FbMqttConnectorPlugin!
 * @subpackage     Entities
 * @since          0.1.0
 *
 * @date           05.03.20
 */

namespace FastyBird\MqttConnectorPlugin\Entities;

/**
 * Device attribute
 *
 * @package        FastyBird:FbMqttConnectorPlugin!
 * @subpackage     Entities
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class DeviceAttribute extends Attribute
{

	public const ALLOWED_ATTRIBUTES = [
		self::NAME,
		self::STATE,
		self::PROPERTIES,
		self::CHANNELS,
		self::EXTENSIONS,
		self::CONTROL,
	];

	/**
	 * {@inheritDoc}
	 */
	protected function getAllowedAttributes(): array
	{
		return self::ALLOWED_ATTRIBUTES;
	}

}
