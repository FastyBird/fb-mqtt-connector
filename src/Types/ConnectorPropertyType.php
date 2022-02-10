<?php declare(strict_types = 1);

/**
 * ConnectorPropertyType.php
 *
 * @license        More in LICENSE.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:FbMqttConnector!
 * @subpackage     Types
 * @since          0.5.0
 *
 * @date           10.02.22
 */

namespace FastyBird\FbMqttConnector\Types;

use Consistence;
use FastyBird\Metadata\Types as MetadataTypes;

/**
 * Connector property name types
 *
 * @package        FastyBird:FbMqttConnector!
 * @subpackage     Types
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
class ConnectorPropertyType extends Consistence\Enum\Enum
{

	/**
	 * Define device states
	 */
	public const NAME_SERVER = MetadataTypes\ConnectorPropertyNameType::NAME_SERVER;
	public const NAME_PORT = MetadataTypes\ConnectorPropertyNameType::NAME_PORT;
	public const NAME_SECURED_PORT = MetadataTypes\ConnectorPropertyNameType::NAME_SECURED_PORT;
	public const NAME_USERNAME = 'username';
	public const NAME_PASSWORD = 'password';
	public const NAME_PROTOCOL = 'protocol';

	/**
	 * @return string
	 */
	public function __toString(): string
	{
		return strval(self::getValue());
	}

}
