<?php declare(strict_types = 1);

/**
 * FbMqttConnector.php
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

use FastyBird\DevicesModule\Entities as DevicesModuleEntities;
use FastyBird\FbMqttConnector\Constants;
use FastyBird\FbMqttConnector\Types;
use FastyBird\Metadata\Types as MetadataTypes;

/**
 * @ORM\Entity
 */
class FbMqttConnector extends DevicesModuleEntities\Connectors\Connector implements IFbMqttConnector
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
		$property = $this->findProperty(Types\ConnectorPropertyType::NAME_SERVER);

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
		$property = $this->findProperty(Types\ConnectorPropertyType::NAME_PORT);

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
		$property = $this->findProperty(Types\ConnectorPropertyType::NAME_SECURED_PORT);

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
		$property = $this->findProperty(Types\ConnectorPropertyType::NAME_USERNAME);

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
		$property = $this->findProperty(Types\ConnectorPropertyType::NAME_PASSWORD);

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
	public function getProtocol(): Types\ProtocolVersionType
	{
		$property = $this->findProperty(Types\ConnectorPropertyType::NAME_PROTOCOL);

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
	public function toArray(): array
	{
		return array_merge(parent::toArray(), [
			'server'       => $this->getServer(),
			'port'         => $this->getPort(),
			'secured_port' => $this->getSecuredPort(),
			'username'     => $this->getUsername(),
			'protocol'     => $this->getProtocol()->getValue(),
		]);
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
	public function getSource()
	{
		return MetadataTypes\ConnectorSourceType::get(MetadataTypes\ConnectorSourceType::SOURCE_CONNECTOR_FB_MQTT);
	}

}
