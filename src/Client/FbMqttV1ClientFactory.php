<?php declare(strict_types = 1);

/**
 * FbMqttV1Client.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:FbMqttConnector!
 * @subpackage     Client
 * @since          0.25.0
 *
 * @date           05.07.22
 */

namespace FastyBird\FbMqttConnector\Client;

use FastyBird\FbMqttConnector\Types;
use FastyBird\Metadata\Entities as MetadataEntities;

/**
 * FastyBird MQTT v1 client factory
 *
 * @package        FastyBird:FbMqttConnector!
 * @subpackage     Client
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface FbMqttV1ClientFactory extends ClientFactory
{

	public const VERSION = Types\ProtocolVersionType::VERSION_1;

	/**
	 * @param MetadataEntities\Modules\DevicesModule\IConnectorEntity $connector
	 *
	 * @return FbMqttV1Client
	 */
	public function create(MetadataEntities\Modules\DevicesModule\IConnectorEntity $connector): FbMqttV1Client;

}
