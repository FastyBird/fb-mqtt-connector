<?php declare(strict_types = 1);

/**
 * DeviceMessageConsumer.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:FbMqttConnector!
 * @subpackage     Consumers
 * @since          0.4.0
 *
 * @date           05.02.22
 */

namespace FastyBird\FbMqttConnector\Consumers;

use Doctrine\DBAL;
use FastyBird\DevicesModule\Entities as DevicesModuleEntities;
use FastyBird\DevicesModule\Models as DevicesModuleModels;
use FastyBird\DevicesModule\Queries as DevicesModuleQueries;
use FastyBird\FbMqttConnector\Consumers;
use FastyBird\FbMqttConnector\Entities;
use FastyBird\FbMqttConnector\Helpers;
use FastyBird\FbMqttConnector\Types;
use FastyBird\Metadata;
use FastyBird\Metadata\Types as MetadataTypes;
use Nette;
use Nette\Utils;
use Psr\Log;

/**
 * Device attributes MQTT message consumer
 *
 * @package        FastyBird:FbMqttConnector!
 * @subpackage     Consumers
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class DeviceMessageConsumer implements Consumers\IConsumer
{

	use Nette\SmartObject;

	/** @var DevicesModuleModels\Devices\IDevicesRepository */
	private DevicesModuleModels\Devices\IDevicesRepository $deviceRepository;

	/** @var DevicesModuleModels\Devices\IDevicesManager */
	private DevicesModuleModels\Devices\IDevicesManager $devicesManager;

	/** @var DevicesModuleModels\Devices\Properties\IPropertiesManager */
	private DevicesModuleModels\Devices\Properties\IPropertiesManager $devicePropertiesManager;

	/** @var DevicesModuleModels\Devices\Controls\IControlsManager */
	private DevicesModuleModels\Devices\Controls\IControlsManager $deviceControlManager;

	/** @var DevicesModuleModels\Devices\Attributes\IAttributesManager */
	private DevicesModuleModels\Devices\Attributes\IAttributesManager $deviceAttributesManager;

	/** @var DevicesModuleModels\Channels\IChannelsManager */
	private DevicesModuleModels\Channels\IChannelsManager $channelsManager;

	/** @var DevicesModuleModels\DataStorage\IDevicesRepository */
	private DevicesModuleModels\DataStorage\IDevicesRepository $deviceDataStorageRepository;

	/** @var DevicesModuleModels\States\DeviceConnectionStateManager */
	private DevicesModuleModels\States\DeviceConnectionStateManager $deviceConnectionStateManager;

	/** @var Helpers\DatabaseHelper */
	private Helpers\DatabaseHelper $databaseHelper;

	/** @var Log\LoggerInterface */
	private Log\LoggerInterface $logger;

	/**
	 * @param DevicesModuleModels\Devices\IDevicesRepository $deviceRepository
	 * @param DevicesModuleModels\Devices\IDevicesManager $devicesManager
	 * @param DevicesModuleModels\Devices\Properties\IPropertiesManager $devicePropertiesManager
	 * @param DevicesModuleModels\Devices\Controls\IControlsManager $deviceControlManager
	 * @param DevicesModuleModels\Devices\Attributes\IAttributesManager $deviceAttributesManager
	 * @param DevicesModuleModels\Channels\IChannelsManager $channelsManager
	 * @param DevicesModuleModels\DataStorage\IDevicesRepository $deviceDataStorageRepository
	 * @param DevicesModuleModels\States\DeviceConnectionStateManager $deviceConnectionStateManager
	 * @param Helpers\DatabaseHelper $databaseHelper
	 * @param Log\LoggerInterface|null $logger
	 */
	public function __construct(
		DevicesModuleModels\Devices\IDevicesRepository $deviceRepository,
		DevicesModuleModels\Devices\IDevicesManager $devicesManager,
		DevicesModuleModels\Devices\Properties\IPropertiesManager $devicePropertiesManager,
		DevicesModuleModels\Devices\Controls\IControlsManager $deviceControlManager,
		DevicesModuleModels\Devices\Attributes\IAttributesManager $deviceAttributesManager,
		DevicesModuleModels\Channels\IChannelsManager $channelsManager,
		DevicesModuleModels\DataStorage\IDevicesRepository $deviceDataStorageRepository,
		DevicesModuleModels\States\DeviceConnectionStateManager $deviceConnectionStateManager,
		Helpers\DatabaseHelper $databaseHelper,
		?Log\LoggerInterface $logger = null
	) {
		$this->deviceRepository = $deviceRepository;
		$this->devicesManager = $devicesManager;
		$this->devicePropertiesManager = $devicePropertiesManager;
		$this->deviceControlManager = $deviceControlManager;
		$this->deviceAttributesManager = $deviceAttributesManager;
		$this->channelsManager = $channelsManager;

		$this->deviceDataStorageRepository = $deviceDataStorageRepository;

		$this->deviceConnectionStateManager = $deviceConnectionStateManager;

		$this->databaseHelper = $databaseHelper;

		$this->logger = $logger ?? new Log\NullLogger();
	}

	/**
	 * {@inheritDoc}
	 *
	 * @throws DBAL\Exception
	 */
	public function consume(
		Entities\Messages\IEntity $entity
	): bool {
		if (!$entity instanceof Entities\Messages\DeviceAttributeEntity) {
			return false;
		}

		if ($entity->getAttribute() === Entities\Messages\AttributeEntity::STATE) {
			$device = $this->deviceDataStorageRepository->findByIdentifier($entity->getConnector(), $entity->getDevice());

			if ($device === null) {
				$this->logger->error(
					sprintf('Device "%s" is not registered', $entity->getDevice()),
					[
						'source' => Metadata\Constants::CONNECTOR_FB_MQTT_SOURCE,
						'type'   => 'device-message-consumer',
						'device' => [
							'identifier' => $entity->getDevice(),
						],
					]
				);

				return true;
			}

			if (MetadataTypes\ConnectionStateType::isValidValue($entity->getValue())) {
				$this->deviceConnectionStateManager->setState(
					$device,
					MetadataTypes\ConnectionStateType::get($entity->getValue())
				);
			}
		} else {
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
						'type'   => 'device-message-consumer',
						'device' => [
							'identifier' => $entity->getDevice(),
						],
					]
				);

				return true;
			}

			$this->databaseHelper->transaction(function () use ($entity, $device): void {
				$toUpdate = [];

				if ($entity->getAttribute() === Entities\Messages\AttributeEntity::NAME) {
					$toUpdate['name'] = $entity->getValue();
				}

				if ($entity->getAttribute() === Entities\Messages\AttributeEntity::PROPERTIES && is_array($entity->getValue())) {
					$this->setDeviceProperties($device, Utils\ArrayHash::from($entity->getValue()));
				}

				if ($entity->getAttribute() === Entities\Messages\AttributeEntity::EXTENSIONS && is_array($entity->getValue())) {
					$this->setDeviceExtensions($device, Utils\ArrayHash::from($entity->getValue()));
				}

				if ($entity->getAttribute() === Entities\Messages\AttributeEntity::CHANNELS && is_array($entity->getValue())) {
					$this->setDeviceChannels($device, Utils\ArrayHash::from($entity->getValue()));
				}

				if ($entity->getAttribute() === Entities\Messages\AttributeEntity::CONTROLS && is_array($entity->getValue())) {
					$this->setDeviceControls($device, Utils\ArrayHash::from($entity->getValue()));
				}

				if ($toUpdate !== []) {
					$this->devicesManager->update($device, Utils\ArrayHash::from($toUpdate));
				}
			});
		}

		$this->logger->debug(
			'Consumed device message',
			[
				'source' => Metadata\Constants::CONNECTOR_FB_MQTT_SOURCE,
				'type'   => 'device-message-consumer',
				'device' => [
					'id' => $device->getId()->toString(),
				],
				'data'   => $entity->toArray(),
			]
		);

		return true;
	}

	/**
	 * @param DevicesModuleEntities\Devices\IDevice $device
	 * @param Utils\ArrayHash<string> $properties
	 *
	 * @return void
	 */
	private function setDeviceProperties(
		DevicesModuleEntities\Devices\IDevice $device,
		Utils\ArrayHash $properties
	): void {
		foreach ($properties as $propertyName) {
			if ($propertyName === Types\DevicePropertyIdentifierType::IDENTIFIER_STATE) {
				$this->deviceConnectionStateManager->setState(
					$device,
					MetadataTypes\ConnectionStateType::get(MetadataTypes\ConnectionStateType::STATE_UNKNOWN)
				);
			} else {
				if ($device->findProperty($propertyName) === null) {
					if (in_array($propertyName, [
						Types\DevicePropertyIdentifierType::IDENTIFIER_IP_ADDRESS,
						Types\DevicePropertyIdentifierType::IDENTIFIER_STATUS_LED,
					], true)) {
						$this->devicePropertiesManager->create(Utils\ArrayHash::from([
							'entity'     => DevicesModuleEntities\Devices\Properties\DynamicProperty::class,
							'device'     => $device,
							'identifier' => $propertyName,
							'name'       => $propertyName,
							'settable'   => false,
							'queryable'  => false,
							'dataType'   => MetadataTypes\DataTypeType::get(MetadataTypes\DataTypeType::DATA_TYPE_STRING),
						]));

					} elseif (in_array($propertyName, [
						Types\DevicePropertyIdentifierType::IDENTIFIER_UPTIME,
						Types\DevicePropertyIdentifierType::IDENTIFIER_FREE_HEAP,
						Types\DevicePropertyIdentifierType::IDENTIFIER_CPU_LOAD,
						Types\DevicePropertyIdentifierType::IDENTIFIER_VCC,
						Types\DevicePropertyIdentifierType::IDENTIFIER_RSSI,
					], true)) {
						$this->devicePropertiesManager->create(Utils\ArrayHash::from([
							'entity'     => DevicesModuleEntities\Devices\Properties\DynamicProperty::class,
							'device'     => $device,
							'identifier' => $propertyName,
							'name'       => $propertyName,
							'settable'   => false,
							'queryable'  => false,
							'dataType'   => MetadataTypes\DataTypeType::get(MetadataTypes\DataTypeType::DATA_TYPE_UINT),
						]));

					} else {
						$this->devicePropertiesManager->create(Utils\ArrayHash::from([
							'entity'     => DevicesModuleEntities\Devices\Properties\DynamicProperty::class,
							'device'     => $device,
							'identifier' => $propertyName,
							'settable'   => false,
							'queryable'  => false,
							'dataType'   => MetadataTypes\DataTypeType::get(MetadataTypes\DataTypeType::DATA_TYPE_UNKNOWN),
						]));
					}
				}
			}
		}

		// Cleanup for unused properties
		foreach ($device->getProperties() as $property) {
			if (!in_array($property->getIdentifier(), (array) $properties, true)) {
				$this->devicePropertiesManager->delete($property);
			}
		}
	}

	/**
	 * @param DevicesModuleEntities\Devices\IDevice $device
	 * @param Utils\ArrayHash<string> $extensions
	 *
	 * @return void
	 */
	private function setDeviceExtensions(
		DevicesModuleEntities\Devices\IDevice $device,
		Utils\ArrayHash $extensions
	): void {
		foreach ($extensions as $extensionName) {
			if ($extensionName === Types\ExtensionTypeType::EXTENSION_TYPE_FASTYBIRD_HARDWARE) {
				foreach ([
							 Types\DeviceAttributeIdentifierType::IDENTIFIER_HARDWARE_MAC_ADDRESS,
							 Types\DeviceAttributeIdentifierType::IDENTIFIER_HARDWARE_MANUFACTURER,
							 Types\DeviceAttributeIdentifierType::IDENTIFIER_HARDWARE_MODEL,
							 Types\DeviceAttributeIdentifierType::IDENTIFIER_HARDWARE_VERSION,
						 ] as $attributeName) {
					if ($device->findAttribute($attributeName) === null) {
						$this->deviceAttributesManager->create(Utils\ArrayHash::from([
							'device'     => $device,
							'identifier' => $attributeName,
						]));
					}
				}
			} elseif ($extensionName === Types\ExtensionTypeType::EXTENSION_TYPE_FASTYBIRD_FIRMWARE) {
				foreach ([
							 Types\DeviceAttributeIdentifierType::IDENTIFIER_FIRMWARE_MANUFACTURER,
							 Types\DeviceAttributeIdentifierType::IDENTIFIER_FIRMWARE_NAME,
							 Types\DeviceAttributeIdentifierType::IDENTIFIER_FIRMWARE_VERSION,
						 ] as $attributeName) {
					if ($device->findAttribute($attributeName) === null) {
						$this->deviceAttributesManager->create(Utils\ArrayHash::from([
							'device'     => $device,
							'identifier' => $attributeName,
						]));
					}
				}
			}
		}
	}

	/**
	 * @param DevicesModuleEntities\Devices\IDevice $device
	 * @param Utils\ArrayHash<string> $controls
	 *
	 * @return void
	 */
	private function setDeviceControls(
		DevicesModuleEntities\Devices\IDevice $device,
		Utils\ArrayHash $controls
	): void {
		foreach ($controls as $controlName) {
			if ($device->findControl($controlName) === null) {
				$this->deviceControlManager->create(Utils\ArrayHash::from([
					'device' => $device,
					'name'   => $controlName,
				]));
			}
		}

		// Cleanup for unused control
		foreach ($device->getControls() as $control) {
			if (!in_array($control->getName(), (array) $controls, true)) {
				$this->deviceControlManager->delete($control);
			}
		}
	}

	/**
	 * @param DevicesModuleEntities\Devices\IDevice $device
	 * @param Utils\ArrayHash<string> $channels
	 *
	 * @return void
	 */
	private function setDeviceChannels(
		DevicesModuleEntities\Devices\IDevice $device,
		Utils\ArrayHash $channels
	): void {
		foreach ($channels as $channelName) {
			if ($device->findChannel($channelName) === null) {
				$this->channelsManager->create(Utils\ArrayHash::from([
					'device'     => $device,
					'identifier' => $channelName,
				]));
			}
		}

		// Cleanup for unused channels
		foreach ($device->getChannels() as $channel) {
			if (!in_array($channel->getIdentifier(), (array) $channels, true)) {
				$this->channelsManager->delete($channel);
			}
		}
	}

}
