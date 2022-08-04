<?php declare(strict_types = 1);

/**
 * ConnectorHelper.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:FbMqttConnector!
 * @subpackage     Helpers
 * @since          0.25.0
 *
 * @date           04.08.22
 */

namespace FastyBird\FbMqttConnector\Helpers;

use FastyBird\DevicesModule\Models as DevicesModuleModels;
use FastyBird\FbMqttConnector;
use FastyBird\FbMqttConnector\Types;
use FastyBird\Metadata\Entities as MetadataEntities;
use Nette;
use Ramsey\Uuid;

/**
 * Useful connector helpers
 *
 * @package        FastyBird:FbMqttConnector!
 * @subpackage     Helpers
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class ConnectorHelper
{

	use Nette\SmartObject;

	/** @var DevicesModuleModels\DataStorage\IConnectorPropertiesRepository */
	private DevicesModuleModels\DataStorage\IConnectorPropertiesRepository $propertiesRepository;

	/**
	 * @param DevicesModuleModels\DataStorage\IConnectorPropertiesRepository $propertiesRepository
	 */
	public function __construct(
		DevicesModuleModels\DataStorage\IConnectorPropertiesRepository $propertiesRepository
	) {
		$this->propertiesRepository = $propertiesRepository;
	}

	/**
	 * @param Uuid\UuidInterface $connectorId
	 * @param Types\ConnectorPropertyIdentifierType $type
	 *
	 * @return float|bool|int|string|null
	 */
	public function getConfiguration(
		Uuid\UuidInterface $connectorId,
		Types\ConnectorPropertyIdentifierType $type
	): float|bool|int|string|null {
		$configuration = $this->propertiesRepository->findByIdentifier($connectorId, strval($type->getValue()));

		if ($configuration instanceof MetadataEntities\Modules\DevicesModule\IConnectorStaticPropertyEntity) {
			if ($type->getValue() === Types\ConnectorPropertyIdentifierType::IDENTIFIER_SERVER) {
				return is_string($configuration->getValue()) ? $configuration->getValue() : null;
			}

			if (
				$type->getValue() === Types\ConnectorPropertyIdentifierType::IDENTIFIER_PORT
				|| $type->getValue() === Types\ConnectorPropertyIdentifierType::IDENTIFIER_SECURED_PORT
			) {
				return is_numeric($configuration->getValue()) ? $configuration->getValue() : null;
			}

			if (
				$type->getValue() === Types\ConnectorPropertyIdentifierType::IDENTIFIER_USERNAME
				|| $type->getValue() === Types\ConnectorPropertyIdentifierType::IDENTIFIER_PASSWORD
			) {
				return is_string($configuration->getValue()) ? $configuration->getValue() : null;
			}

			if ($type->getValue() === Types\ConnectorPropertyIdentifierType::IDENTIFIER_PROTOCOL_VERSION) {
				return Types\ProtocolVersionType::isValidValue($type->getValue()) ? $configuration->getValue() : null;
			}

			return $configuration->getValue();
		}

		if ($type->getValue() === Types\ConnectorPropertyIdentifierType::IDENTIFIER_SERVER) {
			return FbMqttConnector\Constants::BROKER_LOCALHOST_ADDRESS;
		}

		if ($type->getValue() === Types\ConnectorPropertyIdentifierType::IDENTIFIER_PORT) {
			return FbMqttConnector\Constants::BROKER_LOCALHOST_PORT;
		}

		if ($type->getValue() === Types\ConnectorPropertyIdentifierType::IDENTIFIER_SECURED_PORT) {
			return FbMqttConnector\Constants::BROKER_LOCALHOST_SECURED_PORT;
		}

		return null;
	}

}
