<?php declare(strict_types = 1);

/**
 * ChannelPropertyMessageConsumer.php
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
use FastyBird\DevicesModule\Models as DevicesModuleModels;
use FastyBird\DevicesModule\Queries as DevicesModuleQueries;
use FastyBird\FbMqttConnector\Consumers;
use FastyBird\FbMqttConnector\Entities;
use FastyBird\FbMqttConnector\Exceptions;
use Nette;
use Nette\Utils;
use Psr\Log;
use Throwable;

/**
 * Device channel property MQTT message consumer
 *
 * @package        FastyBird:FbMqttConnector!
 * @subpackage     Consumers
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class ChannelPropertyMessageConsumer implements Consumers\IConsumer
{

	use Nette\SmartObject;
	use TPropertyMessageConsumer;

	/** @var DevicesModuleModels\Devices\IDevicesRepository */
	private DevicesModuleModels\Devices\IDevicesRepository $deviceRepository;

	/** @var DevicesModuleModels\Channels\IChannelsRepository */
	private DevicesModuleModels\Channels\IChannelsRepository $channelRepository;

	/** @var DevicesModuleModels\Channels\Properties\IPropertiesManager */
	private DevicesModuleModels\Channels\Properties\IPropertiesManager $propertiesManager;

	/** @var DevicesModuleModels\States\IChannelPropertiesManager|null */
	private ?DevicesModuleModels\States\IChannelPropertiesManager $propertiesStatesManager;

	/** @var DevicesModuleModels\States\IChannelPropertiesRepository|null */
	private ?DevicesModuleModels\States\IChannelPropertiesRepository $propertyStateRepository;

	/** @var Persistence\ManagerRegistry */
	protected Persistence\ManagerRegistry $managerRegistry;

	/** @var Log\LoggerInterface */
	private Log\LoggerInterface $logger;

	public function __construct(
		DevicesModuleModels\Devices\IDevicesRepository $deviceRepository,
		DevicesModuleModels\Channels\IChannelsRepository $channelRepository,
		DevicesModuleModels\Channels\Properties\IPropertiesManager $propertiesManager,
		?DevicesModuleModels\States\IChannelPropertiesManager $propertiesStatesManager,
		?DevicesModuleModels\States\IChannelPropertiesRepository $propertyStateRepository,
		Persistence\ManagerRegistry $managerRegistry,
		?Log\LoggerInterface $logger = null
	) {
		$this->deviceRepository = $deviceRepository;
		$this->channelRepository = $channelRepository;
		$this->propertiesManager = $propertiesManager;
		$this->propertiesStatesManager = $propertiesStatesManager;
		$this->propertyStateRepository = $propertyStateRepository;

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
		if (!$entity instanceof Entities\Messages\ChannelProperty) {
			return;
		}

		$findDeviceQuery = new DevicesModuleQueries\FindDevicesQuery();
		$findDeviceQuery->byIdentifier($entity->getDevice());

		$device = $this->deviceRepository->findOneBy($findDeviceQuery);

		if ($device === null) {
			$this->logger->error(sprintf('[FB:NODE:MQTT] Device "%s" is not registered', $entity->getDevice()));

			return;
		}

		$findChannelQuery = new DevicesModuleQueries\FindChannelsQuery();
		$findChannelQuery->forDevice($device);
		$findChannelQuery->byIdentifier($entity->getChannel());

		$channel = $this->channelRepository->findOneBy($findChannelQuery);

		if ($channel === null) {
			$this->logger->error(sprintf('[FB:NODE:MQTT] Device channel "%s" is not registered', $entity->getChannel()));

			return;
		}

		$property = $channel->findProperty($entity->getProperty());

		if ($property === null) {
			$this->logger->error(sprintf('[FB:NODE:MQTT] Property "%s" is not registered', $entity->getProperty()));

			return;
		}

		try {
			// Start transaction connection to the database
			$this->getOrmConnection()->beginTransaction();

			$toUpdate = $this->handlePropertyConfiguration($entity);

			$this->propertiesManager->update($property, Utils\ArrayHash::from($toUpdate));

			// Commit all changes into database
			$this->getOrmConnection()->commit();

			if (
				$entity->getValue() !== 'N/A'
				&& $this->propertyStateRepository !== null
				&& $this->propertiesStatesManager !== null
			) {
				$propertyState = $this->propertyStateRepository->findOne($property);

				// In case synchronization failed...
				if ($propertyState === null) {
					// ...create state in storage
					$this->propertiesStatesManager->create(
						$property,
						Utils\ArrayHash::from(array_merge(
							$property->toArray(),
							[
								'actual_value'   => $entity->getValue(),
								'expected_value' => null,
								'pending'        => false,
							]
						))
					);

				} else {
					$this->propertiesStatesManager->update(
						$property,
						$propertyState,
						Utils\ArrayHash::from([
							'actual_value'   => $entity->getValue(),
							'expected_value' => null,
							'pending'        => false,
						])
					);
				}
			}
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
