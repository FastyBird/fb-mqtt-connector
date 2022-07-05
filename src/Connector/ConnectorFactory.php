<?php declare(strict_types = 1);

/**
 * Constants.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:FbMqttConnector!
 * @subpackage     Connector
 * @since          0.25.0
 *
 * @date           04.07.22
 */

namespace FastyBird\FbMqttConnector\Connector;

use FastyBird\DevicesModule\Connectors as DevicesModuleConnectors;
use FastyBird\DevicesModule\Exceptions as DevicesModuleExceptions;
use FastyBird\DevicesModule\Models as DevicesModuleModels;
use FastyBird\FbMqttConnector\Client;
use FastyBird\FbMqttConnector\Entities;
use FastyBird\FbMqttConnector\Types;
use FastyBird\Metadata\Entities as MetadataEntities;
use ReflectionClass;

/**
 * Connector service factory
 *
 * @package        FastyBird:FbMqttConnector!
 * @subpackage     Connector
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class ConnectorFactory implements DevicesModuleConnectors\IConnectorFactory
{

	/** @var Client\ClientFactory[] */
	private array $clientsFactories;

	/** @var DevicesModuleModels\DataStorage\IConnectorPropertiesRepository */
	private DevicesModuleModels\DataStorage\IConnectorPropertiesRepository $connectorPropertiesRepository;

	/**
	 * @param Client\ClientFactory[] $clientsFactories
	 * @param DevicesModuleModels\DataStorage\IConnectorPropertiesRepository $connectorPropertiesRepository
	 */
	public function __construct(
		array $clientsFactories,
		DevicesModuleModels\DataStorage\IConnectorPropertiesRepository $connectorPropertiesRepository
	) {
		$this->clientsFactories = $clientsFactories;
		$this->connectorPropertiesRepository = $connectorPropertiesRepository;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getType(): string
	{
		return Entities\FbMqttConnector::CONNECTOR_TYPE;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @throws DevicesModuleExceptions\TerminateException
	 */
	public function create(
		MetadataEntities\Modules\DevicesModule\IConnectorEntity $connector
	): DevicesModuleConnectors\IConnector {
		$versionProperty = $this->connectorPropertiesRepository->findByIdentifier(
			$connector->getId(),
			Types\ConnectorPropertyType::NAME_PROTOCOL_VERSION
		);

		if (
			!$versionProperty instanceof MetadataEntities\Modules\DevicesModule\IConnectorStaticPropertyEntity
			|| !Types\ProtocolVersionType::isValidValue($versionProperty->getValue())
		) {
			throw new DevicesModuleExceptions\TerminateException('Connector protocol version is not configured');
		}

		foreach ($this->clientsFactories as $clientFactory) {
			$rc = new ReflectionClass($clientFactory);

			$constants = $rc->getConstants();

			if (
				array_key_exists(Client\ClientFactory::VERSION_CONSTANT_NAME, $constants)
				&& $constants[Client\ClientFactory::VERSION_CONSTANT_NAME] === $versionProperty->getValue()
				&& method_exists($clientFactory, 'create')
			) {
				return new Connector($clientFactory->create($connector));
			}
		}

		throw new DevicesModuleExceptions\TerminateException('Connector client is not configured');
	}

}
