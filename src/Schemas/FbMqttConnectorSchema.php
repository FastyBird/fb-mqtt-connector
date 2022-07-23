<?php declare(strict_types = 1);

/**
 * FbMqttConnectorSchema.php
 *
 * @license        More in LICENSE.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:FbMqttConnector!
 * @subpackage     Schemas
 * @since          0.4.0
 *
 * @date           20.02.21
 */

namespace FastyBird\FbMqttConnector\Schemas;

use FastyBird\DevicesModule\Schemas as DevicesModuleSchemas;
use FastyBird\FbMqttConnector\Entities;
use FastyBird\Metadata\Types as MetadataTypes;

/**
 * FastyBird MQTT connector entity schema
 *
 * @package        FastyBird:FbMqttConnector!
 * @subpackage     Schemas
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 *
 * @phpstan-extends DevicesModuleSchemas\Connectors\ConnectorSchema<Entities\IFbMqttConnectorEntity>
 */
final class FbMqttConnectorSchema extends DevicesModuleSchemas\Connectors\ConnectorSchema
{

	/**
	 * Define entity schema type string
	 */
	public const SCHEMA_TYPE = MetadataTypes\ConnectorSourceType::SOURCE_CONNECTOR_FB_MQTT . '/connector/' . Entities\FbMqttConnectorEntity::CONNECTOR_TYPE;

	/**
	 * {@inheritDoc}
	 */
	public function getEntityClass(): string
	{
		return Entities\FbMqttConnectorEntity::class;
	}

	/**
	 * @return string
	 */
	public function getType(): string
	{
		return self::SCHEMA_TYPE;
	}

}
