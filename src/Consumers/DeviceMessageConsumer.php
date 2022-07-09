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
use Doctrine\DBAL\Connection;
use Doctrine\Persistence;
use FastyBird\DevicesModule\Entities as DevicesModuleEntities;
use FastyBird\DevicesModule\Exceptions as DevicesModuleExceptions;
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

	/** @var DevicesModuleModels\Channels\IChannelsRepository */
	private DevicesModuleModels\Channels\IChannelsRepository $channelRepository;

	/** @var DevicesModuleModels\Channels\IChannelsManager */
	private DevicesModuleModels\Channels\IChannelsManager $channelsManager;

	/** @var DevicesModuleModels\States\DevicePropertiesRepository */
	private DevicesModuleModels\States\DevicePropertiesRepository $propertyStateRepository;

	/** @var DevicesModuleModels\States\DevicePropertiesManager */
	private DevicesModuleModels\States\DevicePropertiesManager $propertiesStatesManager;

	/** @var Persistence\ManagerRegistry */
	protected Persistence\ManagerRegistry $managerRegistry;

	/** @var Log\LoggerInterface */
	private Log\LoggerInterface $logger;

	public function __construct(
		DevicesModuleModels\Devices\IDevicesRepository $deviceRepository,
		DevicesModuleModels\Devices\IDevicesManager $devicesManager,
		DevicesModuleModels\Devices\Properties\IPropertiesManager $devicePropertiesManager,
		DevicesModuleModels\Devices\Controls\IControlsManager $deviceControlManager,
		DevicesModuleModels\Devices\Attributes\IAttributesManager $deviceAttributesManager,
		DevicesModuleModels\Channels\IChannelsRepository $channelRepository,
		DevicesModuleModels\Channels\IChannelsManager $channelsManager,
		DevicesModuleModels\States\DevicePropertiesRepository $propertyStateRepository,
		DevicesModuleModels\States\DevicePropertiesManager $propertiesStatesManager,
		Persistence\ManagerRegistry $managerRegistry,
		?Log\LoggerInterface $logger = null
	) {
		$this->deviceRepository = $deviceRepository;
		$this->devicesManager = $devicesManager;
		$this->devicePropertiesManager = $devicePropertiesManager;
		$this->deviceControlManager = $deviceControlManager;
		$this->deviceAttributesManager = $deviceAttributesManager;
		$this->channelRepository = $channelRepository;
		$this->channelsManager = $channelsManager;
		$this->propertyStateRepository = $propertyStateRepository;
		$this->propertiesStatesManager = $propertiesStatesManager;

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
		if (!$entity instanceof Entities\Messages\DeviceAttribute) {
			return;
		}

		$findDeviceQuery = new DevicesModuleQueries\FindDevicesQuery();
		$findDeviceQuery->byIdentifier($entity->getDevice());

		$device = $this->deviceRepository->findOneBy($findDeviceQuery);

		if ($device === null) {
			$this->logger->error(
				sprintf('Device "%s" is not registered', $entity->getDevice()),
				[
					'source' => 'fastybird-fb-mqtt-connector',
					'type'   => 'consumer',
				]
			);

			return;
		}

		if ($entity->getAttribute() === Entities\Messages\Attribute::STATE) {
			if (MetadataTypes\ConnectionStateType::isValidValue($entity->getValue())) {
				$this->setDeviceState(
					$device,
					MetadataTypes\ConnectionStateType::get($entity->getValue())
				);
			}
		} else {
			try {
				// Start transaction connection to the database
				$this->getOrmConnection()->beginTransaction();

				$toUpdate = [];

				if ($entity->getAttribute() === Entities\Messages\Attribute::NAME) {
					$toUpdate['name'] = $entity->getValue();
				}

				if ($entity->getAttribute() === Entities\Messages\Attribute::PROPERTIES && is_array($entity->getValue())) {
					$this->setDeviceProperties($device, Utils\ArrayHash::from($entity->getValue()));
				}

				if ($entity->getAttribute() === Entities\Messages\Attribute::EXTENSIONS && is_array($entity->getValue())) {
					$this->setDeviceExtensions($device, Utils\ArrayHash::from($entity->getValue()));
				}

				if ($entity->getAttribute() === Entities\Messages\Attribute::CHANNELS && is_array($entity->getValue())) {
					$this->setDeviceChannels($device, Utils\ArrayHash::from($entity->getValue()));
				}

				if ($entity->getAttribute() === Entities\Messages\Attribute::CONTROLS && is_array($entity->getValue())) {
					$this->setDeviceControls($device, Utils\ArrayHash::from($entity->getValue()));
				}

				if ($toUpdate !== []) {
					$this->devicesManager->update($device, Utils\ArrayHash::from($toUpdate));
				}

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
			if ($propertyName === MetadataTypes\DevicePropertyNameType::NAME_STATE) {
				$this->setDeviceState(
					$device,
					MetadataTypes\ConnectionStateType::get(MetadataTypes\ConnectionStateType::STATE_UNKNOWN)
				);
			} else {
				if (!$device->hasProperty($propertyName)) {
					if (in_array($propertyName, [
						MetadataTypes\DevicePropertyNameType::NAME_IP_ADDRESS,
						MetadataTypes\DevicePropertyNameType::NAME_STATUS_LED,
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
						MetadataTypes\DevicePropertyNameType::NAME_UPTIME,
						MetadataTypes\DevicePropertyNameType::NAME_FREE_HEAP,
						MetadataTypes\DevicePropertyNameType::NAME_CPU_LOAD,
						MetadataTypes\DevicePropertyNameType::NAME_VCC,
						MetadataTypes\DevicePropertyNameType::NAME_RSSI,
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
			if ($extensionName === ExtensionTypeType::EXTENSION_TYPE_FASTYBIRD_HARDWARE) {
				foreach ([
							 MetadataTypes\DeviceAttributeNameType::ATTRIBUTE_HARDWARE_MAC_ADDRESS,
							 MetadataTypes\DeviceAttributeNameType::ATTRIBUTE_HARDWARE_MANUFACTURER,
							 MetadataTypes\DeviceAttributeNameType::ATTRIBUTE_HARDWARE_MODEL,
							 MetadataTypes\DeviceAttributeNameType::ATTRIBUTE_HARDWARE_VERSION,
						 ] as $attributeName) {
					if (!$device->hasAttribute($attributeName)) {
						$this->deviceAttributesManager->create(Utils\ArrayHash::from([
							'device'     => $device,
							'identifier' => $attributeName,
						]));
					}
				}
			} elseif ($extensionName === ExtensionTypeType::EXTENSION_TYPE_FASTYBIRD_FIRMWARE) {
				foreach ([
							 MetadataTypes\DeviceAttributeNameType::ATTRIBUTE_FIRMWARE_MANUFACTURER,
							 MetadataTypes\DeviceAttributeNameType::ATTRIBUTE_FIRMWARE_NAME,
							 MetadataTypes\DeviceAttributeNameType::ATTRIBUTE_FIRMWARE_VERSION,
						 ] as $attributeName) {
					if (!$device->hasAttribute($attributeName)) {
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
			if (!$device->hasControl($controlName)) {
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
		foreach ($channels as $channelId) {
			$findChannelQuery = new DevicesModuleQueries\FindChannelsQuery();
			$findChannelQuery->forDevice($device);
			$findChannelQuery->byIdentifier($channelId);

			// Check if channel exists
			$channel = $this->channelRepository->findOneBy($findChannelQuery);

			// ...if not, create it
			if ($channel === null) {
				$this->channelsManager->create(Utils\ArrayHash::from([
					'device'     => $device,
					'identifier' => $channelId,
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

	/**
	 * @param DevicesModuleEntities\Devices\IDevice $device
	 * @param MetadataTypes\ConnectionStateType $state
	 *
	 * @return void
	 */
	private function setDeviceState(
		DevicesModuleEntities\Devices\IDevice $device,
		MetadataTypes\ConnectionStateType $state
	): void {
		$stateProperty = $device->findProperty(MetadataTypes\DevicePropertyNameType::NAME_STATE);

		if ($stateProperty === null) {
			$stateProperty = $this->devicePropertiesManager->create(Utils\ArrayHash::from([
				'entity'     => DevicesModuleEntities\Devices\Properties\DynamicProperty::class,
				'device'     => $device,
				'identifier' => MetadataTypes\DevicePropertyNameType::NAME_STATE,
				'settable'   => false,
				'queryable'  => false,
				'dataType'   => MetadataTypes\DataTypeType::get(MetadataTypes\DataTypeType::DATA_TYPE_ENUM),
				'unit'       => null,
				'invalid'    => null,
				'format'     => [
					MetadataTypes\ConnectionStateType::STATE_CONNECTED,
					MetadataTypes\ConnectionStateType::STATE_DISCONNECTED,
					MetadataTypes\ConnectionStateType::STATE_INIT,
					MetadataTypes\ConnectionStateType::STATE_READY,
					MetadataTypes\ConnectionStateType::STATE_RUNNING,
					MetadataTypes\ConnectionStateType::STATE_SLEEPING,
					MetadataTypes\ConnectionStateType::STATE_STOPPED,
					MetadataTypes\ConnectionStateType::STATE_LOST,
					MetadataTypes\ConnectionStateType::STATE_ALERT,
					MetadataTypes\ConnectionStateType::STATE_UNKNOWN,
				],
			]));
		}

		try {
			$statePropertyState = $this->propertyStateRepository->findOne($stateProperty);

		} catch (DevicesModuleExceptions\NotImplementedException $ex) {
			$this->logger->warning(
				'States repository is not configured. State could not be fetched',
				[
					'source' => 'fastybird-fb-mqtt-connector',
					'type'   => 'consumer',
				]
			);

			return;
		}

		if ($statePropertyState === null) {
			try {
				$this->propertiesStatesManager->create($stateProperty, Utils\ArrayHash::from([
					'actualValue'   => $state->getValue(),
					'expectedValue' => null,
					'pending'       => false,
					'valid'         => true,
				]));

			} catch (DevicesModuleExceptions\NotImplementedException $ex) {
				$this->logger->warning(
					'States manager is not configured. State could not be saved',
					[
						'source' => 'fastybird-fb-mqtt-connector',
						'type'   => 'consumer',
					]
				);
			}
		} else {
			try {
				$this->propertiesStatesManager->update($stateProperty, $statePropertyState, Utils\ArrayHash::from([
					'actualValue'   => $state->getValue(),
					'expectedValue' => null,
					'pending'       => false,
					'valid'         => true,
				]));

			} catch (DevicesModuleExceptions\NotImplementedException $ex) {
				$this->logger->warning(
					'States manager is not configured. State could not be saved',
					[
						'source' => 'fastybird-fb-mqtt-connector',
						'type'   => 'consumer',
					]
				);
			}
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
