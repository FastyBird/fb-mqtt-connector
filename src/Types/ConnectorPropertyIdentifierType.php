<?php declare(strict_types = 1);

/**
 * ConnectorPropertyIdentifierType.php
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
class ConnectorPropertyIdentifierType extends Consistence\Enum\Enum
{

	/**
	 * Define device states
	 */
	public const IDENTIFIER_SERVER = MetadataTypes\ConnectorPropertyNameType::NAME_SERVER;
	public const IDENTIFIER_PORT = MetadataTypes\ConnectorPropertyNameType::NAME_PORT;
	public const IDENTIFIER_SECURED_PORT = MetadataTypes\ConnectorPropertyNameType::NAME_SECURED_PORT;
	public const IDENTIFIER_USERNAME = 'username';
	public const IDENTIFIER_PASSWORD = 'password';
	public const IDENTIFIER_PROTOCOL_VERSION = 'protocol';

	/**
	 * @return string
	 */
	public function __toString(): string
	{
		return strval(self::getValue());
	}

}
