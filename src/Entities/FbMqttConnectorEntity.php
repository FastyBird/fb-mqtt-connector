<?php declare(strict_types = 1);

/**
 * FbMqttConnectorEntity.php
 *
 * @license        More in LICENSE.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:FbMqttConnector!
 * @subpackage     Entities
 * @since          0.4.0
 *
 * @date           23.01.22
 */

namespace FastyBird\FbMqttConnector\Entities;

use Doctrine\ORM\Mapping as ORM;
use FastyBird\DevicesModule\Entities as DevicesModuleEntities;
use FastyBird\FbMqttConnector\Constants;
use FastyBird\FbMqttConnector\Types;
use FastyBird\Metadata\Types as MetadataTypes;

/**
 * @ORM\Entity
 */
class FbMqttConnectorEntity extends DevicesModuleEntities\Connectors\Connector implements IFbMqttConnectorEntity
{

	public const CONNECTOR_TYPE = 'fb-mqtt';

	/**
	 * {@inheritDoc}
	 */
	public function getType(): string
	{
		return self::CONNECTOR_TYPE;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getServer(): string
	{
		$property = $this->findProperty(Types\ConnectorPropertyIdentifierType::IDENTIFIER_SERVER);

		if (
			!$property instanceof DevicesModuleEntities\Connectors\Properties\IStaticProperty
			|| !is_string($property->getValue())
		) {
			return Constants::BROKER_LOCALHOST_ADDRESS;
		}

		return $property->getValue();
	}

	/**
	 * {@inheritDoc}
	 */
	public function getPort(): int
	{
		$property = $this->findProperty(Types\ConnectorPropertyIdentifierType::IDENTIFIER_PORT);

		if (
			!$property instanceof DevicesModuleEntities\Connectors\Properties\IStaticProperty
			|| !is_int($property->getValue())
		) {
			return Constants::BROKER_LOCALHOST_PORT;
		}

		return $property->getValue();
	}

	/**
	 * {@inheritDoc}
	 */
	public function getSecuredPort(): int
	{
		$property = $this->findProperty(Types\ConnectorPropertyIdentifierType::IDENTIFIER_SECURED_PORT);

		if (
			!$property instanceof DevicesModuleEntities\Connectors\Properties\IStaticProperty
			|| !is_int($property->getValue())
		) {
			return Constants::BROKER_LOCALHOST_SECURED_PORT;
		}

		return $property->getValue();
	}

	/**
	 * {@inheritDoc}
	 */
	public function getUsername(): ?string
	{
		$property = $this->findProperty(Types\ConnectorPropertyIdentifierType::IDENTIFIER_USERNAME);

		if (
			!$property instanceof DevicesModuleEntities\Connectors\Properties\IStaticProperty
			|| !is_string($property->getValue())
		) {
			return null;
		}

		return $property->getValue();
	}

	/**
	 * {@inheritDoc}
	 */
	public function getPassword(): ?string
	{
		$property = $this->findProperty(Types\ConnectorPropertyIdentifierType::IDENTIFIER_PASSWORD);

		if (
			!$property instanceof DevicesModuleEntities\Connectors\Properties\IStaticProperty
			|| !is_string($property->getValue())
		) {
			return null;
		}

		return $property->getValue();
	}

	/**
	 * {@inheritDoc}
	 */
	public function getVersion(): Types\ProtocolVersionType
	{
		$property = $this->findProperty(Types\ConnectorPropertyIdentifierType::IDENTIFIER_PROTOCOL_VERSION);

		if (
			!$property instanceof DevicesModuleEntities\Connectors\Properties\IStaticProperty
			|| !is_numeric($property->getValue())
			|| !Types\ProtocolVersionType::isValidValue($property->getValue())
		) {
			return Types\ProtocolVersionType::get(Types\ProtocolVersionType::VERSION_1);
		}

		return Types\ProtocolVersionType::get($property->getValue());
	}

	/**
	 * {@inheritDoc}
	 */
	public function getDiscriminatorName(): string
	{
		return self::CONNECTOR_TYPE;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getSource(): MetadataTypes\ModuleSourceType|MetadataTypes\ConnectorSourceType|MetadataTypes\PluginSourceType
	{
		return MetadataTypes\ConnectorSourceType::get(MetadataTypes\ConnectorSourceType::SOURCE_CONNECTOR_FB_MQTT);
	}

}
