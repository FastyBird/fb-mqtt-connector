<?php declare(strict_types = 1);

/**
 * ExtensionTypeType.php
 *
 * @license        More in LICENSE.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:FbMqttConnector!
 * @subpackage     Types
 * @since          0.25.0
 *
 * @date           05.07.22
 */

namespace FastyBird\FbMqttConnector\Types;

use Consistence;

/**
 * Extension types
 *
 * @package        FastyBird:FbMqttConnector!
 * @subpackage     Types
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
class ExtensionTypeType extends Consistence\Enum\Enum
{

	/**
	 * Define states
	 */
	public const EXTENSION_TYPE_FASTYBIRD_HARDWARE = 'com.fastybird.hardware';
	public const EXTENSION_TYPE_FASTYBIRD_FIRMWARE = 'com.fastybird.firmware';

	/**
	 * @return string
	 */
	public function __toString(): string
	{
		return strval(self::getValue());
	}

}
