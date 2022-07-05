<?php declare(strict_types = 1);

/**
 * ExtensionAttributeMessageConsumer.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:FbMqttConnector!
 * @subpackage     Consumers
 * @since          0.25.0
 *
 * @date           05.07.22
 */

namespace FastyBird\FbMqttConnector\Consumers;

use Doctrine\DBAL;
use Doctrine\DBAL\Connection;
use Doctrine\Persistence;
use FastyBird\DevicesModule\Models as DevicesModuleModels;
use FastyBird\DevicesModule\Queries as DevicesModuleQueries;
use FastyBird\FbMqttConnector\Consumers;
use FastyBird\FbMqttConnector\Entities;
use FastyBird\FbMqttConnector\Exceptions;
use FastyBird\FbMqttConnector\Types\ExtensionTypeType;
use FastyBird\Metadata\Types as MetadataTypes;
use Nette;
use Nette\Utils;
use Psr\Log;
use Throwable;

/**
 * Device extension MQTT message consumer
 *
 * @package        FastyBird:FbMqttConnector!
 * @subpackage     Consumers
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class ExtensionAttributeMessageConsumer implements Consumers\IConsumer
{

	use Nette\SmartObject;

	/** @var DevicesModuleModels\Devices\IDevicesRepository */
	private DevicesModuleModels\Devices\IDevicesRepository $deviceRepository;

	/** @var DevicesModuleModels\Devices\Attributes\IAttributesRepository */
	private DevicesModuleModels\Devices\Attributes\IAttributesRepository $attributesRepository;

	/** @var DevicesModuleModels\Devices\Attributes\IAttributesManager */
	private DevicesModuleModels\Devices\Attributes\IAttributesManager $attributesManager;

	/** @var Persistence\ManagerRegistry */
	protected Persistence\ManagerRegistry $managerRegistry;

	/** @var Log\LoggerInterface */
	private Log\LoggerInterface $logger;

	public function __construct(
		DevicesModuleModels\Devices\IDevicesRepository $deviceRepository,
		DevicesModuleModels\Devices\Attributes\IAttributesRepository $attributesRepository,
		DevicesModuleModels\Devices\Attributes\IAttributesManager $attributesManager,
		Persistence\ManagerRegistry $managerRegistry,
		?Log\LoggerInterface $logger = null
	) {
		$this->deviceRepository = $deviceRepository;
		$this->attributesRepository = $attributesRepository;
		$this->attributesManager = $attributesManager;

		$this->managerRegistry = $managerRegistry;

		$this->logger = $logger ?? new Log\NullLogger();
	}

	/**
	 * {@inheritDoc}
	 *
	 * @throws DBAL\Exception
	 */
	public function consume(
		Entities\Messages\IEntity $entity
	): void {
		if (!$entity instanceof Entities\Messages\ExtensionAttribute) {
			return;
		}

		$findDeviceQuery = new DevicesModuleQueries\FindDevicesQuery();
		$findDeviceQuery->byIdentifier($entity->getDevice());

		$device = $this->deviceRepository->findOneBy($findDeviceQuery);

		if ($device === null) {
			$this->logger->error(
				sprintf('Device "%s" is not registered', $entity->getDevice()),
				[
					'source'    => 'fastybird-fb-mqtt-connector',
					'type'      => 'consumer',
				]
			);

			return;
		}

		$attributeIdentifier = null;

		// HARDWARE INFO
		if (
			$entity->getExtension()->equalsValue(ExtensionTypeType::EXTENSION_TYPE_FASTYBIRD_HARDWARE)
			&& $entity->getParameter() === Entities\Messages\ExtensionAttribute::MANUFACTURER
		) {
			$attributeIdentifier = MetadataTypes\DeviceAttributeNameType::ATTRIBUTE_HARDWARE_MANUFACTURER;

        } elseif (
			$entity->getExtension()->equalsValue(ExtensionTypeType::EXTENSION_TYPE_FASTYBIRD_HARDWARE)
			&& $entity->getParameter() === Entities\Messages\ExtensionAttribute::MODEL
		) {
			$attributeIdentifier = MetadataTypes\DeviceAttributeNameType::ATTRIBUTE_HARDWARE_MODEL;

        } elseif (
			$entity->getExtension()->equalsValue(ExtensionTypeType::EXTENSION_TYPE_FASTYBIRD_HARDWARE)
			&& $entity->getParameter() === Entities\Messages\ExtensionAttribute::VERSION
		) {
			$attributeIdentifier = MetadataTypes\DeviceAttributeNameType::ATTRIBUTE_HARDWARE_VERSION;

        } elseif (
			$entity->getExtension()->equalsValue(ExtensionTypeType::EXTENSION_TYPE_FASTYBIRD_HARDWARE)
			&& $entity->getParameter() === Entities\Messages\ExtensionAttribute::MAC_ADDRESS
		) {
			$attributeIdentifier = MetadataTypes\DeviceAttributeNameType::ATTRIBUTE_HARDWARE_MAC_ADDRESS;

        // FIRMWARE INFO
		} elseif (
			$entity->getExtension()->equalsValue(ExtensionTypeType::EXTENSION_TYPE_FASTYBIRD_FIRMWARE)
			&& $entity->getParameter() === Entities\Messages\ExtensionAttribute::MANUFACTURER
		) {
			$attributeIdentifier = MetadataTypes\DeviceAttributeNameType::ATTRIBUTE_FIRMWARE_MANUFACTURER;

        } elseif (
			$entity->getExtension()->equalsValue(ExtensionTypeType::EXTENSION_TYPE_FASTYBIRD_FIRMWARE)
			&& $entity->getParameter() === Entities\Messages\ExtensionAttribute::NAME
		) {
			$attributeIdentifier = MetadataTypes\DeviceAttributeNameType::ATTRIBUTE_FIRMWARE_NAME;

        } elseif (
			$entity->getExtension()->equalsValue(ExtensionTypeType::EXTENSION_TYPE_FASTYBIRD_FIRMWARE)
			&& $entity->getParameter() === Entities\Messages\ExtensionAttribute::VERSION
		) {
			$attributeIdentifier = MetadataTypes\DeviceAttributeNameType::ATTRIBUTE_FIRMWARE_VERSION;
		}

		if ($attributeIdentifier === null) {
			return;
		}

		$attribute = $device->findAttribute($attributeIdentifier);

		if ($attribute === null) {
			$this->logger->error(
				sprintf('Device attribute "%s" is not registered', $entity->getParameter()),
				[
					'source'    => 'fastybird-fb-mqtt-connector',
					'type'      => 'consumer',
				]
			);

			return;
		}

		try {
			// Start transaction connection to the database
			$this->getOrmConnection()->beginTransaction();

			$toUpdate = [
				'content' => $entity->getValue(),
			];

			$this->attributesManager->update($attribute, Utils\ArrayHash::from($toUpdate));

			// Commit all changes into database
			$this->getOrmConnection()->commit();

		} catch (Throwable $ex) {
			// Revert all changes when error occur
			if ($this->getOrmConnection()->isTransactionActive()) {
				$this->getOrmConnection()->rollBack();
			}

			throw new Exceptions\InvalidStateException('An error occurred: ' . $ex->getMessage(), $ex->getCode(), $ex);
		}
	}

	/**
	 * @return Connection
	 */
	private function getOrmConnection(): Connection
	{
		$connection = $this->managerRegistry->getConnection();

		if ($connection instanceof Connection) {
			return $connection;
		}

		throw new Exceptions\RuntimeException('Entity manager could not be loaded');
	}

}
