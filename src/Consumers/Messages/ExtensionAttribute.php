<?php declare(strict_types = 1);

/**
 * ExtensionAttribute.php
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

namespace FastyBird\FbMqttConnector\Consumers\Messages;

use Doctrine\DBAL;
use FastyBird\DevicesModule\Entities as DevicesModuleEntities;
use FastyBird\DevicesModule\Models as DevicesModuleModels;
use FastyBird\DevicesModule\Queries as DevicesModuleQueries;
use FastyBird\FbMqttConnector\Consumers;
use FastyBird\FbMqttConnector\Entities;
use FastyBird\FbMqttConnector\Helpers;
use FastyBird\FbMqttConnector\Types;
use FastyBird\Metadata;
use Nette;
use Nette\Utils;
use Psr\Log;

/**
 * Device extension MQTT message consumer
 *
 * @package        FastyBird:FbMqttConnector!
 * @subpackage     Consumers
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class ExtensionAttribute implements Consumers\Consumer
{

	use Nette\SmartObject;

	/** @var DevicesModuleModels\Devices\IDevicesRepository */
	private DevicesModuleModels\Devices\IDevicesRepository $deviceRepository;

	/** @var DevicesModuleModels\Devices\Attributes\IAttributesManager */
	private DevicesModuleModels\Devices\Attributes\IAttributesManager $attributesManager;

	/** @var Helpers\Database */
	private Helpers\Database $databaseHelper;

	/** @var Log\LoggerInterface */
	private Log\LoggerInterface $logger;

	/**
	 * @param DevicesModuleModels\Devices\IDevicesRepository $deviceRepository
	 * @param DevicesModuleModels\Devices\Attributes\IAttributesManager $attributesManager
	 * @param Helpers\Database $databaseHelper
	 * @param Log\LoggerInterface|null $logger
	 */
	public function __construct(
		DevicesModuleModels\Devices\IDevicesRepository $deviceRepository,
		DevicesModuleModels\Devices\Attributes\IAttributesManager $attributesManager,
		Helpers\Database $databaseHelper,
		?Log\LoggerInterface $logger = null
	) {
		$this->deviceRepository = $deviceRepository;
		$this->attributesManager = $attributesManager;

		$this->databaseHelper = $databaseHelper;

		$this->logger = $logger ?? new Log\NullLogger();
	}

	/**
	 * {@inheritDoc}
	 *
	 * @throws DBAL\Exception
	 */
	public function consume(
		Entities\Messages\Entity $entity
	): bool {
		if (!$entity instanceof Entities\Messages\ExtensionAttribute) {
			return false;
		}

		/** @var DevicesModuleEntities\Devices\IDevice|null $device */
		$device = $this->databaseHelper->query(function () use ($entity): ?DevicesModuleEntities\Devices\IDevice {
			$findDeviceQuery = new DevicesModuleQueries\FindDevicesQuery();
			$findDeviceQuery->byIdentifier($entity->getDevice());

			return $this->deviceRepository->findOneBy($findDeviceQuery);
		});

		if ($device === null) {
			$this->logger->error(
				sprintf('Device "%s" is not registered', $entity->getDevice()),
				[
					'source' => Metadata\Constants::CONNECTOR_FB_MQTT_SOURCE,
					'type'   => 'extension-attribute-message-consumer',
					'device' => [
						'identifier' => $entity->getDevice(),
					],
				]
			);

			return true;
		}

		$attributeIdentifier = null;

		// HARDWARE INFO
		if (
			$entity->getExtension()->equalsValue(Types\ExtensionType::EXTENSION_TYPE_FASTYBIRD_HARDWARE)
			&& $entity->getParameter() === Entities\Messages\ExtensionAttribute::MANUFACTURER
		) {
			$attributeIdentifier = Types\DeviceAttributeIdentifier::IDENTIFIER_HARDWARE_MANUFACTURER;

		} elseif (
			$entity->getExtension()->equalsValue(Types\ExtensionType::EXTENSION_TYPE_FASTYBIRD_HARDWARE)
			&& $entity->getParameter() === Entities\Messages\ExtensionAttribute::MODEL
		) {
			$attributeIdentifier = Types\DeviceAttributeIdentifier::IDENTIFIER_HARDWARE_MODEL;

		} elseif (
			$entity->getExtension()->equalsValue(Types\ExtensionType::EXTENSION_TYPE_FASTYBIRD_HARDWARE)
			&& $entity->getParameter() === Entities\Messages\ExtensionAttribute::VERSION
		) {
			$attributeIdentifier = Types\DeviceAttributeIdentifier::IDENTIFIER_HARDWARE_VERSION;

		} elseif (
			$entity->getExtension()->equalsValue(Types\ExtensionType::EXTENSION_TYPE_FASTYBIRD_HARDWARE)
			&& $entity->getParameter() === Entities\Messages\ExtensionAttribute::MAC_ADDRESS
		) {
			$attributeIdentifier = Types\DeviceAttributeIdentifier::IDENTIFIER_HARDWARE_MAC_ADDRESS;

			// FIRMWARE INFO
		} elseif (
			$entity->getExtension()->equalsValue(Types\ExtensionType::EXTENSION_TYPE_FASTYBIRD_FIRMWARE)
			&& $entity->getParameter() === Entities\Messages\ExtensionAttribute::MANUFACTURER
		) {
			$attributeIdentifier = Types\DeviceAttributeIdentifier::IDENTIFIER_FIRMWARE_MANUFACTURER;

		} elseif (
			$entity->getExtension()->equalsValue(Types\ExtensionType::EXTENSION_TYPE_FASTYBIRD_FIRMWARE)
			&& $entity->getParameter() === Entities\Messages\ExtensionAttribute::NAME
		) {
			$attributeIdentifier = Types\DeviceAttributeIdentifier::IDENTIFIER_FIRMWARE_NAME;

		} elseif (
			$entity->getExtension()->equalsValue(Types\ExtensionType::EXTENSION_TYPE_FASTYBIRD_FIRMWARE)
			&& $entity->getParameter() === Entities\Messages\ExtensionAttribute::VERSION
		) {
			$attributeIdentifier = Types\DeviceAttributeIdentifier::IDENTIFIER_FIRMWARE_VERSION;
		}

		if ($attributeIdentifier === null) {
			return true;
		}

		$attribute = $device->findAttribute($attributeIdentifier);

		if ($attribute === null) {
			$this->logger->error(
				sprintf('Device attribute "%s" is not registered', $entity->getParameter()),
				[
					'source'    => Metadata\Constants::CONNECTOR_FB_MQTT_SOURCE,
					'type'      => 'extension-attribute-message-consumer',
					'device'    => [
						'identifier' => $entity->getDevice(),
					],
					'attribute' => [
						'identifier' => $entity->getParameter(),
					],
				]
			);

			return true;
		}

		$this->databaseHelper->transaction(function () use ($entity, $attribute): void {
			$toUpdate = [
				'content' => $entity->getValue(),
			];

			$this->attributesManager->update($attribute, Utils\ArrayHash::from($toUpdate));
		});

		$this->logger->debug(
			'Consumed extension attribute message',
			[
				'source' => Metadata\Constants::CONNECTOR_FB_MQTT_SOURCE,
				'type'   => 'extension-attribute-message-consumer',
				'device' => [
					'id' => $device->getId()->toString(),
				],
				'data'   => $entity->toArray(),
			]
		);

		return true;
	}

}
