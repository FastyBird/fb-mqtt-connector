<?php declare(strict_types = 1);

/**
 * DevicePropertyIdentifierType.php
 *
 * @license        More in LICENSE.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:FbMqttConnector!
 * @subpackage     Types
 * @since          0.25.0
 *
 * @date           23.07.22
 */

namespace FastyBird\FbMqttConnector\Types;

use Consistence;
use FastyBird\Metadata\Types as MetadataTypes;

/**
 * Device property identifier types
 *
 * @package        FastyBird:FbMqttConnector!
 * @subpackage     Types
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
class DevicePropertyIdentifierType extends Consistence\Enum\Enum
{

	/**
	 * Define device states
	 */
	public const IDENTIFIER_STATE = MetadataTypes\DevicePropertyNameType::NAME_STATE;
	public const IDENTIFIER_IP_ADDRESS = MetadataTypes\DevicePropertyNameType::NAME_IP_ADDRESS;
	public const IDENTIFIER_STATUS_LED = MetadataTypes\DevicePropertyNameType::NAME_STATUS_LED;
	public const IDENTIFIER_UPTIME = MetadataTypes\DevicePropertyNameType::NAME_UPTIME;
	public const IDENTIFIER_FREE_HEAP = MetadataTypes\DevicePropertyNameType::NAME_FREE_HEAP;
	public const IDENTIFIER_CPU_LOAD = MetadataTypes\DevicePropertyNameType::NAME_CPU_LOAD;
	public const IDENTIFIER_VCC = MetadataTypes\DevicePropertyNameType::NAME_VCC;
	public const IDENTIFIER_RSSI = MetadataTypes\DevicePropertyNameType::NAME_RSSI;
	public const IDENTIFIER_USERNAME = 'username';
	public const IDENTIFIER_PASSWORD = 'password';
	public const IDENTIFIER_AUTH_ENABLED = 'auth-enabled';

	/**
	 * @return string
	 */
	public function __toString(): string
	{
		return strval(self::getValue());
	}

}
