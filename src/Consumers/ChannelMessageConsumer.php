<?php declare(strict_types = 1);

/**
 * ChannelMessageConsumer.php
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
 * Device channel attributes MQTT message consumer
 *
 * @package        FastyBird:FbMqttConnector!
 * @subpackage     Consumers
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class ChannelMessageConsumer implements Consumers\IConsumer
{

	use Nette\SmartObject;

	/** @var DevicesModuleModels\Devices\IDevicesRepository */
	private DevicesModuleModels\Devices\IDevicesRepository $deviceRepository;

	/** @var DevicesModuleModels\Channels\IChannelsRepository */
	private DevicesModuleModels\Channels\IChannelsRepository $channelRepository;

	/** @var DevicesModuleModels\Channels\IChannelsManager */
	private DevicesModuleModels\Channels\IChannelsManager $channelsManager;

	/** @var DevicesModuleModels\Channels\Properties\IPropertiesManager */
	private DevicesModuleModels\Channels\Properties\IPropertiesManager $channelPropertiesManager;

	/** @var DevicesModuleModels\Channels\Controls\IControlsManager */
	private DevicesModuleModels\Channels\Controls\IControlsManager $channelControlManager;

	/** @var Persistence\ManagerRegistry */
	protected Persistence\ManagerRegistry $managerRegistry;

	/** @var Log\LoggerInterface */
	private Log\LoggerInterface $logger;

	public function __construct(
		DevicesModuleModels\Devices\IDevicesRepository $deviceRepository,
		DevicesModuleModels\Channels\IChannelsRepository $channelRepository,
		DevicesModuleModels\Channels\IChannelsManager $channelsManager,
		DevicesModuleModels\Channels\Properties\IPropertiesManager $channelPropertiesManager,
		DevicesModuleModels\Channels\Controls\IControlsManager $channelControlManager,
		Persistence\ManagerRegistry $managerRegistry,
		?Log\LoggerInterface $logger = null
	) {
		$this->deviceRepository = $deviceRepository;
		$this->channelRepository = $channelRepository;
		$this->channelsManager = $channelsManager;
		$this->channelPropertiesManager = $channelPropertiesManager;
		$this->channelControlManager = $channelControlManager;

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
		if (!$entity instanceof Entities\Messages\ChannelAttribute) {
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

		$findChannelQuery = new DevicesModuleQueries\FindChannelsQuery();
		$findChannelQuery->forDevice($device);
		$findChannelQuery->byIdentifier($entity->getChannel());

		$channel = $this->channelRepository->findOneBy($findChannelQuery);

		if ($channel === null) {
			$this->logger->error(
				sprintf('Device channel "%s" is not registered', $entity->getChannel()),
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

			$toUpdate = [];

			if ($entity->getAttribute() === Entities\Messages\Attribute::NAME) {
				$toUpdate['name'] = $entity->getValue();
			}

			if ($entity->getAttribute() === Entities\Messages\Attribute::PROPERTIES && is_array($entity->getValue())) {
				$this->setChannelProperties($channel, Utils\ArrayHash::from($entity->getValue()));
			}

			if ($entity->getAttribute() === Entities\Messages\Attribute::CONTROLS && is_array($entity->getValue())) {
				$this->setChannelControls($channel, Utils\ArrayHash::from($entity->getValue()));
			}

			if ($toUpdate !== []) {
				$this->channelsManager->update($channel, Utils\ArrayHash::from($toUpdate));
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

	/**
	 * @param DevicesModuleEntities\Channels\IChannel $channel
	 * @param Utils\ArrayHash<string> $properties
	 *
	 * @return void
	 */
	private function setChannelProperties(
		DevicesModuleEntities\Channels\IChannel $channel,
		Utils\ArrayHash $properties
	): void {
		foreach ($properties as $propertyName) {
			if (!$channel->hasProperty($propertyName)) {
				$this->channelPropertiesManager->create(Utils\ArrayHash::from([
					'entity'     => DevicesModuleEntities\Channels\Properties\DynamicProperty::class,
					'channel'    => $channel,
					'identifier' => $propertyName,
					'settable'   => false,
					'queryable'  => false,
					'dataType'   => MetadataTypes\DataTypeType::get(MetadataTypes\DataTypeType::DATA_TYPE_UNKNOWN),
				]));
			}
		}

		// Cleanup for unused properties
		foreach ($channel->getProperties() as $property) {
			if (!in_array($property->getIdentifier(), (array) $properties, true)) {
				$this->channelPropertiesManager->delete($property);
			}
		}
	}

	/**
	 * @param DevicesModuleEntities\Channels\IChannel $channel
	 * @param Utils\ArrayHash<string> $controls
	 *
	 * @return void
	 */
	private function setChannelControls(
		DevicesModuleEntities\Channels\IChannel $channel,
		Utils\ArrayHash $controls
	): void {
		foreach ($controls as $controlName) {
			if (!$channel->hasControl($controlName)) {
				$this->channelControlManager->create(Utils\ArrayHash::from([
					'channel' => $channel,
					'name'    => $controlName,
				]));
			}
		}

		// Cleanup for unused control
		foreach ($channel->getControls() as $control) {
			if (!in_array($control->getName(), (array) $controls, true)) {
				$this->channelControlManager->delete($control);
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
