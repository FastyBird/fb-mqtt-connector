<?php declare(strict_types = 1);

/**
 * WriteV1PropertyState.php
 *
 * @license        More in LICENSE.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:FbMqttConnector!
 * @subpackage     Queue
 * @since          1.0.0
 *
 * @date           03.12.23
 */

namespace FastyBird\Connector\FbMqtt\Queue\Consumers;

use DateTimeInterface;
use FastyBird\Connector\FbMqtt;
use FastyBird\Connector\FbMqtt\API;
use FastyBird\Connector\FbMqtt\Entities;
use FastyBird\Connector\FbMqtt\Exceptions;
use FastyBird\Connector\FbMqtt\Helpers;
use FastyBird\Connector\FbMqtt\Queue;
use FastyBird\DateTimeFactory;
use FastyBird\Library\Bootstrap\Helpers as BootstrapHelpers;
use FastyBird\Library\Metadata\Documents as MetadataDocuments;
use FastyBird\Library\Metadata\Exceptions as MetadataExceptions;
use FastyBird\Library\Metadata\Types as MetadataTypes;
use FastyBird\Library\Metadata\Utilities as MetadataUtilities;
use FastyBird\Module\Devices\Exceptions as DevicesExceptions;
use FastyBird\Module\Devices\Models as DevicesModels;
use FastyBird\Module\Devices\Queries as DevicesQueries;
use FastyBird\Module\Devices\States as DevicesStates;
use FastyBird\Module\Devices\Utilities as DevicesUtilities;
use Nette;
use Nette\Utils;
use RuntimeException;
use Throwable;
use function assert;
use function strval;

/**
 * Write V1 protocol state to device message consumer
 *
 * @package        FastyBird:FbMqttConnector!
 * @subpackage     Queue
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class WriteV1PropertyState implements Queue\Consumer
{

	use Nette\SmartObject;

	public function __construct(
		private readonly API\ConnectionManager $connectionManager,
		private readonly Helpers\Connector $connectorHelper,
		private readonly FbMqtt\Logger $logger,
		private readonly DevicesModels\Configuration\Connectors\Repository $connectorsConfigurationRepository,
		private readonly DevicesModels\Configuration\Devices\Repository $devicesConfigurationRepository,
		private readonly DevicesModels\Configuration\Devices\Properties\Repository $devicesPropertiesConfigurationRepository,
		private readonly DevicesModels\Configuration\Channels\Repository $channelsConfigurationRepository,
		private readonly DevicesModels\Configuration\Channels\Properties\Repository $channelsPropertiesConfigurationRepository,
		private readonly DevicesUtilities\DevicePropertiesStates $devicePropertiesStatesManager,
		private readonly DevicesUtilities\ChannelPropertiesStates $channelPropertiesStatesManager,
		private readonly DateTimeFactory\Factory $dateTimeFactory,
	)
	{
	}

	/**
	 * @throws DevicesExceptions\InvalidArgument
	 * @throws DevicesExceptions\InvalidState
	 * @throws Exceptions\InvalidState
	 * @throws Exceptions\Runtime
	 * @throws MetadataExceptions\InvalidArgument
	 * @throws MetadataExceptions\InvalidState
	 * @throws RuntimeException
	 */
	public function consume(Entities\Messages\Entity $entity): bool
	{
		if (
			!$entity instanceof Entities\Messages\WriteDevicePropertyState
			&& !$entity instanceof Entities\Messages\WriteChannelPropertyState
		) {
			return false;
		}

		$now = $this->dateTimeFactory->getNow();

		$findConnectorQuery = new DevicesQueries\Configuration\FindConnectors();
		$findConnectorQuery->byId($entity->getConnector());
		$findConnectorQuery->byType(Entities\FbMqttConnector::TYPE);

		$connector = $this->connectorsConfigurationRepository->findOneBy($findConnectorQuery);

		if ($connector === null) {
			$this->logger->error(
				'Connector could not be loaded',
				[
					'source' => MetadataTypes\ConnectorSource::SOURCE_CONNECTOR_FB_MQTT,
					'type' => 'write-v1-property-state-message-consumer',
					'connector' => [
						'id' => $entity->getConnector()->toString(),
					],
					'device' => [
						'id' => $entity->getDevice()->toString(),
					],
					'property' => [
						'id' => $entity->getProperty()->toString(),
					],
					'data' => $entity->toArray(),
				],
			);

			return true;
		}

		if (!$this->connectorHelper->getProtocolVersion($connector)->equalsValue(
			FbMqtt\Types\ProtocolVersion::VERSION_1,
		)) {
			return false;
		}

		$findDeviceQuery = new DevicesQueries\Configuration\FindDevices();
		$findDeviceQuery->forConnector($connector);
		$findDeviceQuery->byId($entity->getDevice());
		$findDeviceQuery->byType(Entities\FbMqttDevice::TYPE);

		$device = $this->devicesConfigurationRepository->findOneBy($findDeviceQuery);

		if ($device === null) {
			$this->logger->error(
				'Device could not be loaded',
				[
					'source' => MetadataTypes\ConnectorSource::SOURCE_CONNECTOR_FB_MQTT,
					'type' => 'write-v1-property-state-message-consumer',
					'connector' => [
						'id' => $entity->getConnector()->toString(),
					],
					'device' => [
						'id' => $entity->getDevice()->toString(),
					],
					'property' => [
						'id' => $entity->getProperty()->toString(),
					],
					'data' => $entity->toArray(),
				],
			);

			return true;
		}

		$channel = null;

		if ($entity instanceof Entities\Messages\WriteChannelPropertyState) {
			$findChannelQuery = new DevicesQueries\Configuration\FindChannels();
			$findChannelQuery->forDevice($device);
			$findChannelQuery->byId($entity->getChannel());
			$findChannelQuery->byType(Entities\FbMqttChannel::TYPE);

			$channel = $this->channelsConfigurationRepository->findOneBy($findChannelQuery);

			if ($channel === null) {
				$this->logger->error(
					'Channel could not be loaded',
					[
						'source' => MetadataTypes\ConnectorSource::SOURCE_CONNECTOR_FB_MQTT,
						'type' => 'write-v1-property-state-message-consumer',
						'connector' => [
							'id' => $entity->getConnector()->toString(),
						],
						'device' => [
							'id' => $entity->getDevice()->toString(),
						],
						'property' => [
							'id' => $entity->getProperty()->toString(),
						],
						'data' => $entity->toArray(),
					],
				);

				return true;
			}

			$findChannelPropertyQuery = new DevicesQueries\Configuration\FindChannelDynamicProperties();
			$findChannelPropertyQuery->forChannel($channel);
			$findChannelPropertyQuery->byId($entity->getProperty());

			$property = $this->channelsPropertiesConfigurationRepository->findOneBy(
				$findChannelPropertyQuery,
				MetadataDocuments\DevicesModule\ChannelDynamicProperty::class,
			);

			if ($property === null) {
				$this->logger->error(
					'Channel property could not be loaded',
					[
						'source' => MetadataTypes\ConnectorSource::SOURCE_CONNECTOR_FB_MQTT,
						'type' => 'write-v1-property-state-message-consumer',
						'connector' => [
							'id' => $entity->getConnector()->toString(),
						],
						'device' => [
							'id' => $entity->getDevice()->toString(),
						],
						'property' => [
							'id' => $entity->getProperty()->toString(),
						],
						'data' => $entity->toArray(),
					],
				);

				return true;
			}
		} else {
			$findDevicePropertyQuery = new DevicesQueries\Configuration\FindDeviceDynamicProperties();
			$findDevicePropertyQuery->forDevice($device);
			$findDevicePropertyQuery->byId($entity->getProperty());

			$property = $this->devicesPropertiesConfigurationRepository->findOneBy(
				$findDevicePropertyQuery,
				MetadataDocuments\DevicesModule\DeviceDynamicProperty::class,
			);

			if ($property === null) {
				$this->logger->error(
					'Device property could not be loaded',
					[
						'source' => MetadataTypes\ConnectorSource::SOURCE_CONNECTOR_FB_MQTT,
						'type' => 'write-v1-property-state-message-consumer',
						'connector' => [
							'id' => $entity->getConnector()->toString(),
						],
						'device' => [
							'id' => $entity->getDevice()->toString(),
						],
						'property' => [
							'id' => $entity->getProperty()->toString(),
						],
						'data' => $entity->toArray(),
					],
				);

				return true;
			}
		}

		if (!$property->isSettable()) {
			$this->logger->error(
				'Property is not writable',
				[
					'source' => MetadataTypes\ConnectorSource::SOURCE_CONNECTOR_FB_MQTT,
					'type' => 'write-v1-property-state-message-consumer',
					'connector' => [
						'id' => $entity->getConnector()->toString(),
					],
					'device' => [
						'id' => $entity->getDevice()->toString(),
					],
					'property' => [
						'id' => $entity->getProperty()->toString(),
					],
					'data' => $entity->toArray(),
				],
			);

			return true;
		}

		$state = $property instanceof MetadataDocuments\DevicesModule\ChannelDynamicProperty
			? $this->channelPropertiesStatesManager->getValue($property)
			: $this->devicePropertiesStatesManager->getValue($property);

		if ($state === null) {
			return true;
		}

		$expectedValue = MetadataUtilities\ValueHelper::transformValueToDevice(
			$property->getDataType(),
			$property->getFormat(),
			$state->getExpectedValue(),
		);

		if ($expectedValue === null) {
			if ($property instanceof MetadataDocuments\DevicesModule\ChannelDynamicProperty) {
				$this->channelPropertiesStatesManager->setValue(
					$property,
					Utils\ArrayHash::from([
						DevicesStates\Property::EXPECTED_VALUE_FIELD => null,
						DevicesStates\Property::PENDING_FIELD => false,
					]),
				);
			} else {
				$this->devicePropertiesStatesManager->setValue(
					$property,
					Utils\ArrayHash::from([
						DevicesStates\Property::EXPECTED_VALUE_FIELD => null,
						DevicesStates\Property::PENDING_FIELD => false,
					]),
				);
			}

			return true;
		}

		if ($property instanceof MetadataDocuments\DevicesModule\ChannelDynamicProperty) {
			assert($channel instanceof MetadataDocuments\DevicesModule\Channel);

			$this->channelPropertiesStatesManager->setValue(
				$property,
				Utils\ArrayHash::from([
					DevicesStates\Property::PENDING_FIELD => $now->format(DateTimeInterface::ATOM),
				]),
			);

			$topic = API\V1Builder::buildChannelPropertyTopic($device, $channel, $property);

		} else {
			$this->devicePropertiesStatesManager->setValue(
				$property,
				Utils\ArrayHash::from([
					DevicesStates\Property::PENDING_FIELD => $now->format(DateTimeInterface::ATOM),
				]),
			);

			$topic = API\V1Builder::buildDevicePropertyTopic($device, $property);
		}

		$this->connectionManager
			->getConnection($connector)
			->publish(
				$topic,
				strval(MetadataUtilities\ValueHelper::flattenValue($expectedValue)),
			)
			->then(function () use ($property, $now): void {
				if ($property instanceof MetadataDocuments\DevicesModule\ChannelDynamicProperty) {
					$state = $this->channelPropertiesStatesManager->getValue($property);

					if ($state?->getExpectedValue() !== null) {
						$this->channelPropertiesStatesManager->setValue(
							$property,
							Utils\ArrayHash::from([
								DevicesStates\Property::PENDING_FIELD => $now->format(DateTimeInterface::ATOM),
							]),
						);
					}
				} else {
					$state = $this->devicePropertiesStatesManager->getValue($property);

					if ($state?->getExpectedValue() !== null) {
						$this->devicePropertiesStatesManager->setValue(
							$property,
							Utils\ArrayHash::from([
								DevicesStates\Property::PENDING_FIELD => $now->format(DateTimeInterface::ATOM),
							]),
						);
					}
				}
			})
			->catch(function (Throwable $ex) use ($property, $entity): void {
				if ($property instanceof MetadataDocuments\DevicesModule\ChannelDynamicProperty) {
					$this->channelPropertiesStatesManager->setValue(
						$property,
						Utils\ArrayHash::from([
							DevicesStates\Property::EXPECTED_VALUE_FIELD => null,
							DevicesStates\Property::PENDING_FIELD => false,
						]),
					);
				} else {
					$this->devicePropertiesStatesManager->setValue(
						$property,
						Utils\ArrayHash::from([
							DevicesStates\Property::EXPECTED_VALUE_FIELD => null,
							DevicesStates\Property::PENDING_FIELD => false,
						]),
					);
				}

				$this->logger->error(
					'Could write state to device',
					[
						'source' => MetadataTypes\ConnectorSource::SOURCE_CONNECTOR_FB_MQTT,
						'type' => 'write-v1-property-state-message-consumer',
						'exception' => BootstrapHelpers\Logger::buildException($ex),
						'connector' => [
							'id' => $entity->getConnector()->toString(),
						],
						'device' => [
							'id' => $entity->getDevice()->toString(),
						],
						'property' => [
							'id' => $entity->getProperty()->toString(),
						],
						'data' => $entity->toArray(),
					],
				);
			});

		$this->logger->debug(
			'Consumed write device state message',
			[
				'source' => MetadataTypes\ConnectorSource::SOURCE_CONNECTOR_FB_MQTT,
				'type' => 'write-v1-property-state-message-consumer',
				'connector' => [
					'id' => $entity->getConnector()->toString(),
				],
				'device' => [
					'id' => $entity->getDevice()->toString(),
				],
				'property' => [
					'id' => $entity->getProperty()->toString(),
				],
				'data' => $entity->toArray(),
			],
		);

		return true;
	}

}
