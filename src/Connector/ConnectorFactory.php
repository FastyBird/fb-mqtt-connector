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
use Psr\Log;

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

	/** @var Client\IClient[] */
	private array $clients;

	/** @var DevicesModuleModels\DataStorage\IConnectorPropertiesRepository */
	private DevicesModuleModels\DataStorage\IConnectorPropertiesRepository $connectorPropertiesRepository;

	/** @var Log\LoggerInterface */
	private Log\LoggerInterface $logger;

	/**
	 * @param Client\IClient[] $clients
	 * @param DevicesModuleModels\DataStorage\IConnectorPropertiesRepository $connectorPropertiesRepository
	 * @param Log\LoggerInterface|null $logger
	 */
	public function __construct(
		array $clients,
		DevicesModuleModels\DataStorage\IConnectorPropertiesRepository $connectorPropertiesRepository,
		?Log\LoggerInterface $logger = null
	) {
		$this->clients = $clients;
		$this->connectorPropertiesRepository = $connectorPropertiesRepository;

		$this->logger = $logger ?? new Log\NullLogger();
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
			|| Types\ProtocolVersionType::isValidValue($versionProperty->getValue())
		) {
			throw new DevicesModuleExceptions\TerminateException('Connector protocol version is not configured');
		}

		$version = Types\ProtocolVersionType::get($versionProperty->getValue());

		foreach ($this->clients as $client) {
			if ($client->getVersion()->equals($version)) {
				return new Connector(
					$connector,
					$client
				);
			}
		}

		throw new DevicesModuleExceptions\TerminateException('Connector client is not configured');
	}

}
