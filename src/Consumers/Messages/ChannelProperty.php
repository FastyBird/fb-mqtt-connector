<?php declare(strict_types = 1);

/**
 * ChannelProperty.php
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

namespace FastyBird\FbMqttConnector\Consumers\Messages;

use Doctrine\DBAL;
use FastyBird\DevicesModule\Entities as DevicesModuleEntities;
use FastyBird\DevicesModule\Exceptions as DevicesModuleExceptions;
use FastyBird\DevicesModule\Models as DevicesModuleModels;
use FastyBird\DevicesModule\Queries as DevicesModuleQueries;
use FastyBird\DevicesModule\Utilities as DevicesModuleUtilities;
use FastyBird\FbMqttConnector;
use FastyBird\FbMqttConnector\Consumers;
use FastyBird\FbMqttConnector\Entities;
use FastyBird\FbMqttConnector\Helpers;
use FastyBird\Metadata;
use Nette;
use Nette\Utils;
use Psr\Log;

/**
 * Device channel property MQTT message consumer
 *
 * @package        FastyBird:FbMqttConnector!
 * @subpackage     Consumers
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class ChannelProperty implements Consumers\Consumer
{

	use Nette\SmartObject;
	use TProperty;

	/** @var DevicesModuleModels\Devices\IDevicesRepository */
	private DevicesModuleModels\Devices\IDevicesRepository $deviceRepository;

	/** @var DevicesModuleModels\Channels\Properties\IPropertiesRepository */
	private DevicesModuleModels\Channels\Properties\IPropertiesRepository $propertiesRepository;

	/** @var DevicesModuleModels\Channels\Properties\IPropertiesManager */
	private DevicesModuleModels\Channels\Properties\IPropertiesManager $propertiesManager;

	/** @var DevicesModuleModels\DataStorage\IDevicesRepository */
	private DevicesModuleModels\DataStorage\IDevicesRepository $devicesDataStorageRepository;

	/** @var DevicesModuleModels\DataStorage\IChannelsRepository */
	private DevicesModuleModels\DataStorage\IChannelsRepository $channelsDataStorageRepository;

	/** @var DevicesModuleModels\DataStorage\IChannelPropertiesRepository */
	private DevicesModuleModels\DataStorage\IChannelPropertiesRepository $propertiesDataStorageRepository;

	/** @var DevicesModuleModels\States\ChannelPropertiesRepository */
	private DevicesModuleModels\States\ChannelPropertiesRepository $propertyStateRepository;

	/** @var DevicesModuleModels\States\ChannelPropertiesManager */
	private DevicesModuleModels\States\ChannelPropertiesManager $propertiesStatesManager;

	/** @var Helpers\Database */
	private Helpers\Database $databaseHelper;

	/** @var Log\LoggerInterface */
	private Log\LoggerInterface $logger;

	/**
	 * @param DevicesModuleModels\Devices\IDevicesRepository $deviceRepository
	 * @param DevicesModuleModels\Channels\Properties\IPropertiesRepository $propertiesRepository
	 * @param DevicesModuleModels\Channels\Properties\IPropertiesManager $propertiesManager
	 * @param DevicesModuleModels\DataStorage\IDevicesRepository $devicesDataStorageRepository
	 * @param DevicesModuleModels\DataStorage\IChannelsRepository $channelsDataStorageRepository
	 * @param DevicesModuleModels\DataStorage\IChannelPropertiesRepository $propertiesDataStorageRepository
	 * @param DevicesModuleModels\States\ChannelPropertiesManager $propertiesStatesManager
	 * @param DevicesModuleModels\States\ChannelPropertiesRepository $propertyStateRepository
	 * @param Helpers\Database $databaseHelper
	 * @param Log\LoggerInterface|null $logger
	 */
	public function __construct(
		DevicesModuleModels\Devices\IDevicesRepository $deviceRepository,
		DevicesModuleModels\Channels\Properties\IPropertiesRepository $propertiesRepository,
		DevicesModuleModels\Channels\Properties\IPropertiesManager $propertiesManager,
		DevicesModuleModels\DataStorage\IDevicesRepository $devicesDataStorageRepository,
		DevicesModuleModels\DataStorage\IChannelsRepository $channelsDataStorageRepository,
		DevicesModuleModels\DataStorage\IChannelPropertiesRepository $propertiesDataStorageRepository,
		DevicesModuleModels\States\ChannelPropertiesManager $propertiesStatesManager,
		DevicesModuleModels\States\ChannelPropertiesRepository $propertyStateRepository,
		Helpers\Database $databaseHelper,
		?Log\LoggerInterface $logger = null
	) {
		$this->deviceRepository = $deviceRepository;
		$this->propertiesRepository = $propertiesRepository;
		$this->propertiesManager = $propertiesManager;

		$this->devicesDataStorageRepository = $devicesDataStorageRepository;
		$this->channelsDataStorageRepository = $channelsDataStorageRepository;
		$this->propertiesDataStorageRepository = $propertiesDataStorageRepository;

		$this->propertiesStatesManager = $propertiesStatesManager;
		$this->propertyStateRepository = $propertyStateRepository;

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
		if (!$entity instanceof Entities\Messages\ChannelProperty) {
			return false;
		}

		if ($entity->getValue() !== FbMqttConnector\Constants::VALUE_NOT_SET) {
			$deviceItem = $this->devicesDataStorageRepository->findByIdentifier(
				$entity->getConnector(),
				$entity->getDevice()
			);

			if ($deviceItem === null) {
				$this->logger->error(
					sprintf('Device "%s" is not registered', $entity->getDevice()),
					[
						'source' => Metadata\Constants::CONNECTOR_FB_MQTT_SOURCE,
						'type'   => 'channel-property-message-consumer',
						'device' => [
							'identifier' => $entity->getDevice(),
						],
					]
				);

				return true;
			}

			$channelItem = $this->channelsDataStorageRepository->findByIdentifier(
				$deviceItem->getId(),
				$entity->getChannel()
			);

			if ($channelItem === null) {
				$this->logger->error(
					sprintf('Devices channel "%s" is not registered', $entity->getChannel()),
					[
						'source'  => Metadata\Constants::CONNECTOR_FB_MQTT_SOURCE,
						'type'    => 'channel-property-message-consumer',
						'device'  => [
							'identifier' => $entity->getDevice(),
						],
						'channel' => [
							'identifier' => $entity->getChannel(),
						],
					]
				);

				return true;
			}

			$propertyItem = $this->propertiesDataStorageRepository->findByIdentifier(
				$channelItem->getId(),
				$entity->getProperty()
			);

			if ($propertyItem instanceof Metadata\Entities\Modules\DevicesModule\IChannelStaticPropertyEntity) {
				/** @var DevicesModuleEntities\Channels\Properties\IProperty $property */
				$property = $this->databaseHelper->query(
					function () use ($propertyItem): ?DevicesModuleEntities\Channels\Properties\IProperty {
						$findPropertyQuery = new DevicesModuleQueries\FindChannelPropertiesQuery();
						$findPropertyQuery->byId($propertyItem->getId());

						return $this->propertiesRepository->findOneBy($findPropertyQuery);
					}
				);

				$this->databaseHelper->transaction(
					function () use ($entity, $property): DevicesModuleEntities\Channels\Properties\IProperty {
						return $this->propertiesManager->update($property, Utils\ArrayHash::from([
							'value' => $entity->getValue(),
						]));
					}
				);
			} elseif ($propertyItem instanceof Metadata\Entities\Modules\DevicesModule\IChannelDynamicPropertyEntity) {
				try {
					$propertyState = $this->propertyStateRepository->findOne($propertyItem);

				} catch (DevicesModuleExceptions\NotImplementedException) {
					$this->logger->warning(
						'States repository is not configured. State could not be fetched',
						[
							'source'   => Metadata\Constants::CONNECTOR_FB_MQTT_SOURCE,
							'type'     => 'channel-property-message-consumer',
							'device'   => [
								'id' => $deviceItem->getId()->toString(),
							],
							'channel'  => [
								'id' => $channelItem->getId()->toString(),
							],
							'property' => [
								'id' => $propertyItem->getId()->toString(),
							],
						]
					);

					return true;
				}

				$actualValue = DevicesModuleUtilities\ValueHelper::flattenValue(
					DevicesModuleUtilities\ValueHelper::normalizeValue(
						$propertyItem->getDataType(),
						$entity->getValue(),
						$propertyItem->getFormat(),
						$propertyItem->getInvalid()
					)
				);

				try {
					// In case synchronization failed...
					if ($propertyState === null) {
						// ...create state in storage
						$this->propertiesStatesManager->create(
							$propertyItem,
							Utils\ArrayHash::from(array_merge(
								$propertyItem->toArray(),
								[
									'actualValue'   => $actualValue,
									'expectedValue' => null,
									'pending'       => false,
									'valid'         => true,
								]
							))
						);

					} else {
						$this->propertiesStatesManager->update(
							$propertyItem,
							$propertyState,
							Utils\ArrayHash::from([
								'actualValue' => $actualValue,
								'valid'       => true,
							])
						);
					}
				} catch (DevicesModuleExceptions\NotImplementedException) {
					$this->logger->warning(
						'States manager is not configured. State could not be saved',
						[
							'source'   => Metadata\Constants::CONNECTOR_FB_MQTT_SOURCE,
							'type'     => 'channel-property-message-consumer',
							'device'   => [
								'id' => $deviceItem->getId()->toString(),
							],
							'channel'  => [
								'id' => $channelItem->getId()->toString(),
							],
							'property' => [
								'id' => $propertyItem->getId()->toString(),
							],
						]
					);
				}
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
						'type'   => 'channel-property-message-consumer',
						'device' => [
							'identifier' => $entity->getDevice(),
						],
					]
				);

				return true;
			}

			$channel = $device->findChannel($entity->getChannel());

			if ($channel === null) {
				$this->logger->error(
					sprintf('Device channel "%s" is not registered', $entity->getChannel()),
					[
						'source'  => Metadata\Constants::CONNECTOR_FB_MQTT_SOURCE,
						'type'    => 'channel-property-message-consumer',
						'device'  => [
							'identifier' => $entity->getDevice(),
						],
						'channel' => [
							'identifier' => $entity->getChannel(),
						],
					]
				);

				return true;
			}

			$property = $channel->findProperty($entity->getProperty());

			if ($property === null) {
				$this->logger->error(
					sprintf('Property "%s" is not registered', $entity->getProperty()),
					[
						'source'   => Metadata\Constants::CONNECTOR_FB_MQTT_SOURCE,
						'type'     => 'channel-property-message-consumer',
						'device'   => [
							'identifier' => $entity->getDevice(),
						],
						'channel'  => [
							'identifier' => $entity->getChannel(),
						],
						'property' => [
							'identifier' => $entity->getProperty(),
						],
					]
				);

				return true;
			}

			if (count($entity->getAttributes())) {
				$this->databaseHelper->transaction(function () use ($entity, $property): void {
					$toUpdate = $this->handlePropertyConfiguration($entity);

					$this->propertiesManager->update($property, Utils\ArrayHash::from($toUpdate));
				});
			}
		}

		$this->logger->debug(
			'Consumed channel property message',
			[
				'source' => Metadata\Constants::CONNECTOR_FB_MQTT_SOURCE,
				'type'   => 'channel-property-message-consumer',
				'device' => [
					'identifier' => $entity->getDevice(),
				],
				'data'   => $entity->toArray(),
			]
		);

		return true;
	}

}
