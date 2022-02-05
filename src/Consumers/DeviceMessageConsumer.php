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

use Doctrine\DBAL\Connection;
use Doctrine\Persistence;
use FastyBird\DevicesModule\Entities as DevicesModuleEntities;
use FastyBird\DevicesModule\Models as DevicesModuleModels;
use FastyBird\DevicesModule\Queries as DevicesModuleQueries;
use FastyBird\FbMqttConnector\Consumers;
use FastyBird\FbMqttConnector\Entities;
use FastyBird\FbMqttConnector\Exceptions;
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

	/** @var DevicesModuleModels\Devices\IDeviceRepository */
	private DevicesModuleModels\Devices\IDeviceRepository $deviceRepository;

	/** @var DevicesModuleModels\Devices\IDevicesManager */
	private DevicesModuleModels\Devices\IDevicesManager $devicesManager;

	/** @var DevicesModuleModels\Devices\Properties\IPropertiesManager */
	private DevicesModuleModels\Devices\Properties\IPropertiesManager $devicePropertiesManager;

	/** @var DevicesModuleModels\Devices\Controls\IControlsManager */
	private DevicesModuleModels\Devices\Controls\IControlsManager $deviceControlManager;

	/** @var DevicesModuleModels\Channels\IChannelRepository */
	private DevicesModuleModels\Channels\IChannelRepository $channelRepository;

	/** @var DevicesModuleModels\Channels\IChannelsManager */
	private DevicesModuleModels\Channels\IChannelsManager $channelsManager;

	/** @var Persistence\ManagerRegistry */
	protected Persistence\ManagerRegistry $managerRegistry;

	/** @var Log\LoggerInterface */
	private Log\LoggerInterface $logger;

	public function __construct(
		DevicesModuleModels\Devices\IDeviceRepository $deviceRepository,
		DevicesModuleModels\Devices\IDevicesManager $devicesManager,
		DevicesModuleModels\Devices\Properties\IPropertiesManager $devicePropertiesManager,
		DevicesModuleModels\Devices\Controls\IControlsManager $deviceControlManager,
		DevicesModuleModels\Channels\IChannelRepository $channelRepository,
		DevicesModuleModels\Channels\IChannelsManager $channelsManager,
		Persistence\ManagerRegistry $managerRegistry,
		?Log\LoggerInterface $logger = null
	) {
		$this->deviceRepository = $deviceRepository;
		$this->devicesManager = $devicesManager;
		$this->devicePropertiesManager = $devicePropertiesManager;
		$this->deviceControlManager = $deviceControlManager;
		$this->channelRepository = $channelRepository;
		$this->channelsManager = $channelsManager;

		$this->managerRegistry = $managerRegistry;

		$this->logger = $logger ?? new Log\NullLogger();
	}

	/**
	 * {@inheritDoc}
	 *
	 * @throws Exceptions\InvalidStateException
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
			$this->logger->error(sprintf('[FB:NODE:MQTT] Device "%s" is not registered', $entity->getDevice()));

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

				if ($entity->getParent() !== null) {
					$findDeviceQuery = new DevicesModuleQueries\FindDevicesQuery();
					$findDeviceQuery->byIdentifier($entity->getParent());

					$parent = $this->deviceRepository->findOneBy($findDeviceQuery);

					if ($parent !== null) {
						$toUpdate['parent'] = $parent;
					}
				}

				if ($entity->getAttribute() === Entities\Messages\Attribute::NAME) {
					$toUpdate['name'] = $entity->getValue();
				}

				if ($entity->getAttribute() === Entities\Messages\Attribute::PROPERTIES && is_array($entity->getValue())) {
					$this->setDeviceProperties($device, Utils\ArrayHash::from($entity->getValue()));
				}

				if ($entity->getAttribute() === Entities\Messages\Attribute::CHANNELS && is_array($entity->getValue())) {
					$this->setDeviceChannels($device, Utils\ArrayHash::from($entity->getValue()));
				}

				if ($entity->getAttribute() === Entities\Messages\Attribute::CONTROL && is_array($entity->getValue())) {
					$this->setDeviceControl($device, Utils\ArrayHash::from($entity->getValue()));
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
							'dataType'   => MetadataTypes\DataTypeType::DATA_TYPE_STRING,
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
							'dataType'   => MetadataTypes\DataTypeType::DATA_TYPE_UINT,
						]));

					} else {
						$this->devicePropertiesManager->create(Utils\ArrayHash::from([
							'entity'     => DevicesModuleEntities\Devices\Properties\DynamicProperty::class,
							'device'     => $device,
							'identifier' => $propertyName,
							'settable'   => false,
							'queryable'  => false,
							'dataType'   => MetadataTypes\DataTypeType::DATA_TYPE_UNKNOWN,
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
	 * @param Utils\ArrayHash<string> $controls
	 *
	 * @return void
	 */
	private function setDeviceControl(
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
	 * @param MetadataTypes\ConnectionStateType $state
	 *
	 * @return void
	 */
	private function setDeviceState(
		DevicesModuleEntities\Devices\IDevice $device,
		MetadataTypes\ConnectionStateType $state
	): void {
		$stateProperty = $device->getProperty(MetadataTypes\DevicePropertyNameType::NAME_STATE);

		if ($stateProperty === null) {
			$this->devicePropertiesManager->create(Utils\ArrayHash::from([
				'entity'     => DevicesModuleEntities\Devices\Properties\StaticProperty::class,
				'device'     => $device,
				'identifier' => MetadataTypes\DevicePropertyNameType::NAME_STATE,
				'settable'   => false,
				'queryable'  => false,
				'dataType'   => MetadataTypes\DataTypeType::DATA_TYPE_STRING,
				'value'      => $state->getValue(),
			]));

		} else {
			$this->devicePropertiesManager->update($stateProperty, Utils\ArrayHash::from([
				'value' => $state->getValue(),
			]));
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
