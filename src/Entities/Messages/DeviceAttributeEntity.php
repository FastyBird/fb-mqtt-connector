<?php declare(strict_types = 1);

/**
 * DeviceAttributeEntity.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:FbMqttConnector!
 * @subpackage     Entities
 * @since          0.1.0
 *
 * @date           05.03.20
 */

namespace FastyBird\FbMqttConnector\Entities\Messages;

/**
 * Device attribute
 *
 * @package        FastyBird:FbMqttConnector!
 * @subpackage     Entities
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class DeviceAttributeEntity extends AttributeEntity
{

	public const ALLOWED_ATTRIBUTES = [
		self::NAME,
		self::STATE,
		self::PROPERTIES,
		self::CHANNELS,
		self::EXTENSIONS,
		self::CONTROLS,
	];

	/**
	 * {@inheritDoc}
	 */
	protected function getAllowedAttributes(): array
	{
		return self::ALLOWED_ATTRIBUTES;
	}

}
