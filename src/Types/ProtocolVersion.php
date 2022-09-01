<?php declare(strict_types = 1);

/**
 * ProtocolVersion.php
 *
 * @license        More in LICENSE.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:FbMqttConnector!
 * @subpackage     Types
 * @since          0.1.0
 *
 * @date           05.02.22
 */

namespace FastyBird\FbMqttConnector\Types;

use Consistence;

/**
 * Protocol versions
 *
 * @package        FastyBird:FbMqttConnector!
 * @subpackage     Types
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
class ProtocolVersion extends Consistence\Enum\Enum
{

	/**
	 * Define states
	 */
	public const VERSION_1 = 'v1';

	/**
	 * @return string
	 */
	public function __toString(): string
	{
		return strval(self::getValue());
	}

}
