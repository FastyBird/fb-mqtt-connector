<?php declare(strict_types = 1);

/**
 * FbMqttV1Client.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:FbMqttConnector!
 * @subpackage     Clients
 * @since          0.25.0
 *
 * @date           23.02.20
 */

namespace FastyBird\FbMqttConnector\Clients;

use BinSoul\Net\Mqtt;
use DateTimeInterface;
use FastyBird\DateTimeFactory;
use FastyBird\DevicesModule\Models as DevicesModuleModels;
use FastyBird\FbMqttConnector;
use FastyBird\FbMqttConnector\API;
use FastyBird\FbMqttConnector\Consumers;
use FastyBird\FbMqttConnector\Entities;
use FastyBird\FbMqttConnector\Exceptions;
use FastyBird\FbMqttConnector\Types;
use FastyBird\Metadata;
use FastyBird\Metadata\Entities as MetadataEntities;
use FastyBird\Metadata\Types as MetadataTypes;
use Nette\Utils;
use React\EventLoop;
use Throwable;

/**
 * FastyBird MQTT v1 client
 *
 * @package        FastyBird:FbMqttConnector!
 * @subpackage     Clients
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class FbMqttV1Client extends Client
{

	public const MQTT_SYSTEM_TOPIC = '$SYS/broker/log/#';

	// MQTT api topics subscribe format
	public const DEVICES_TOPICS = [
		FbMqttConnector\Constants::MQTT_API_PREFIX . FbMqttConnector\Constants::MQTT_API_V1_VERSION_PREFIX . '/+/+',
		FbMqttConnector\Constants::MQTT_API_PREFIX . FbMqttConnector\Constants::MQTT_API_V1_VERSION_PREFIX . '/+/+/+',
		FbMqttConnector\Constants::MQTT_API_PREFIX . FbMqttConnector\Constants::MQTT_API_V1_VERSION_PREFIX . '/+/+/+/+',
		FbMqttConnector\Constants::MQTT_API_PREFIX . FbMqttConnector\Constants::MQTT_API_V1_VERSION_PREFIX . '/+/+/+/+/+',
		FbMqttConnector\Constants::MQTT_API_PREFIX . FbMqttConnector\Constants::MQTT_API_V1_VERSION_PREFIX . '/+/+/+/+/+/+',
		FbMqttConnector\Constants::MQTT_API_PREFIX . FbMqttConnector\Constants::MQTT_API_V1_VERSION_PREFIX . '/+/+/+/+/+/+/+',
	];

	// When new client is connected, broker send specific payload
	private const NEW_CLIENT_MESSAGE_PAYLOAD = 'New client connected from';

	/** @var string[] */
	private array $processedDevices = [];

	/** @var Array<string, DateTimeInterface> */
	private array $processedProperties = [];

	/** @var API\V1Validator */
	private API\V1Validator $apiValidator;

	/** @var API\V1Parser */
	private API\V1Parser $apiParser;

	/** @var API\V1Builder */
	private API\V1Builder $apiBuilder;

	/** @var DevicesModuleModels\DataStorage\IDevicesRepository */
	private DevicesModuleModels\DataStorage\IDevicesRepository $devicesRepository;

	/** @var DevicesModuleModels\DataStorage\IDevicePropertiesRepository */
	private DevicesModuleModels\DataStorage\IDevicePropertiesRepository $devicePropertiesRepository;

	/** @var DevicesModuleModels\DataStorage\IDeviceControlsRepository */
	private DevicesModuleModels\DataStorage\IDeviceControlsRepository $deviceControlsRepository;

	/** @var DevicesModuleModels\DataStorage\IChannelsRepository */
	private DevicesModuleModels\DataStorage\IChannelsRepository $channelsRepository;

	/** @var DevicesModuleModels\DataStorage\IChannelPropertiesRepository */
	private DevicesModuleModels\DataStorage\IChannelPropertiesRepository $channelPropertiesRepository;

	/** @var DevicesModuleModels\DataStorage\IChannelControlsRepository */
	private DevicesModuleModels\DataStorage\IChannelControlsRepository $channelControlsRepository;

	/** @var DevicesModuleModels\States\DevicePropertiesRepository */
	private DevicesModuleModels\States\DevicePropertiesRepository $devicePropertiesStatesRepository;

	/** @var DevicesModuleModels\States\DevicePropertiesManager */
	private DevicesModuleModels\States\DevicePropertiesManager $devicePropertiesStatesManager;

	/** @var DevicesModuleModels\States\ChannelPropertiesRepository */
	private DevicesModuleModels\States\ChannelPropertiesRepository $channelPropertiesStatesRepository;

	/** @var DevicesModuleModels\States\ChannelPropertiesManager */
	private DevicesModuleModels\States\ChannelPropertiesManager $channelPropertiesStatesManager;

	/** @var DevicesModuleModels\States\DeviceConnectionStateManager */
	private DevicesModuleModels\States\DeviceConnectionStateManager $deviceConnectionStateManager;

	/** @var DateTimeFactory\DateTimeFactory */
	private DateTimeFactory\DateTimeFactory $dateTimeFactory;

	/**
	 * @param MetadataEntities\Modules\DevicesModule\IConnectorEntity $connector
	 * @param API\V1Validator $apiValidator
	 * @param API\V1Parser $apiParser
	 * @param API\V1Builder $apiBuilder
	 * @param Consumers\Consumer $consumer
	 * @param DevicesModuleModels\DataStorage\IConnectorPropertiesRepository $connectorPropertiesRepository
	 * @param DevicesModuleModels\DataStorage\IDevicesRepository $devicesRepository
	 * @param DevicesModuleModels\DataStorage\IDevicePropertiesRepository $devicePropertiesRepository
	 * @param DevicesModuleModels\DataStorage\IDeviceControlsRepository $deviceControlsRepository
	 * @param DevicesModuleModels\DataStorage\IChannelsRepository $channelsRepository
	 * @param DevicesModuleModels\DataStorage\IChannelPropertiesRepository $channelPropertiesRepository
	 * @param DevicesModuleModels\DataStorage\IChannelControlsRepository $channelControlsRepository
	 * @param DevicesModuleModels\States\DevicePropertiesRepository $devicePropertiesStatesRepository
	 * @param DevicesModuleModels\States\DevicePropertiesManager $devicePropertiesStatesManager
	 * @param DevicesModuleModels\States\ChannelPropertiesRepository $channelPropertiesStatesRepository
	 * @param DevicesModuleModels\States\ChannelPropertiesManager $channelPropertiesStatesManager
	 * @param DevicesModuleModels\States\DeviceConnectionStateManager $deviceConnectionStateManager
	 * @param DateTimeFactory\DateTimeFactory $dateTimeFactory
	 * @param EventLoop\LoopInterface $loop
	 * @param Mqtt\ClientIdentifierGenerator|null $identifierGenerator
	 * @param Mqtt\FlowFactory|null $flowFactory
	 * @param Mqtt\StreamParser|null $parser
	 */
	public function __construct(
		MetadataEntities\Modules\DevicesModule\IConnectorEntity $connector,
		API\V1Validator $apiValidator,
		API\V1Parser $apiParser,
		API\V1Builder $apiBuilder,
		Consumers\Consumer $consumer,
		DevicesModuleModels\DataStorage\IConnectorPropertiesRepository $connectorPropertiesRepository,
		DevicesModuleModels\DataStorage\IDevicesRepository $devicesRepository,
		DevicesModuleModels\DataStorage\IDevicePropertiesRepository $devicePropertiesRepository,
		DevicesModuleModels\DataStorage\IDeviceControlsRepository $deviceControlsRepository,
		DevicesModuleModels\DataStorage\IChannelsRepository $channelsRepository,
		DevicesModuleModels\DataStorage\IChannelPropertiesRepository $channelPropertiesRepository,
		DevicesModuleModels\DataStorage\IChannelControlsRepository $channelControlsRepository,
		DevicesModuleModels\States\DevicePropertiesRepository $devicePropertiesStatesRepository,
		DevicesModuleModels\States\DevicePropertiesManager $devicePropertiesStatesManager,
		DevicesModuleModels\States\ChannelPropertiesRepository $channelPropertiesStatesRepository,
		DevicesModuleModels\States\ChannelPropertiesManager $channelPropertiesStatesManager,
		DevicesModuleModels\States\DeviceConnectionStateManager $deviceConnectionStateManager,
		DateTimeFactory\DateTimeFactory $dateTimeFactory,
		EventLoop\LoopInterface $loop,
		?Mqtt\ClientIdentifierGenerator $identifierGenerator = null,
		?Mqtt\FlowFactory $flowFactory = null,
		?Mqtt\StreamParser $parser = null
	) {
		parent::__construct(
			$connector,
			$connectorPropertiesRepository,
			$consumer,
			$loop,
			$identifierGenerator,
			$flowFactory,
			$parser
		);

		$this->apiValidator = $apiValidator;
		$this->apiParser = $apiParser;
		$this->apiBuilder = $apiBuilder;

		$this->devicesRepository = $devicesRepository;
		$this->devicePropertiesRepository = $devicePropertiesRepository;
		$this->deviceControlsRepository = $deviceControlsRepository;
		$this->channelsRepository = $channelsRepository;
		$this->channelPropertiesRepository = $channelPropertiesRepository;
		$this->channelControlsRepository = $channelControlsRepository;

		$this->devicePropertiesStatesRepository = $devicePropertiesStatesRepository;
		$this->devicePropertiesStatesManager = $devicePropertiesStatesManager;
		$this->channelPropertiesStatesRepository = $channelPropertiesStatesRepository;
		$this->channelPropertiesStatesManager = $channelPropertiesStatesManager;
		$this->deviceConnectionStateManager = $deviceConnectionStateManager;

		$this->dateTimeFactory = $dateTimeFactory;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getVersion(): Types\ProtocolVersionType
	{
		return Types\ProtocolVersionType::get(Types\ProtocolVersionType::VERSION_1);
	}

	/**
	 * {@inheritDoc}
	 */
	public function writeDeviceControl(MetadataEntities\Actions\IActionDeviceControlEntity $action): void
	{
		$control = $this->deviceControlsRepository->findById($action->getControl());

		if ($control === null) {
			$this->logger->debug(
				'Controlled device control entity was not found in registry',
				[
					'source'  => Metadata\Constants::CONNECTOR_FB_MQTT_SOURCE,
					'type'    => 'client',
					'control' => [
						'device'  => $action->getDevice()->toString(),
						'control' => $action->getControl()->toString(),
					],
				]
			);

			return;
		}

		$device = $this->devicesRepository->findById($action->getDevice());

		if ($device === null) {
			$this->logger->debug(
				'Controlled device entity was not found in registry',
				[
					'source'  => Metadata\Constants::CONNECTOR_FB_MQTT_SOURCE,
					'type'    => 'client',
					'control' => [
						'device'  => $action->getDevice()->toString(),
						'control' => $action->getControl()->toString(),
					],
				]
			);

			return;
		}

		$this->publish(
			$this->apiBuilder->buildDeviceCommandTopic($device, $control),
			$action->getExpectedValue() !== null ? strval($action->getExpectedValue()) : null
		);
	}

	/**
	 * {@inheritDoc}
	 */
	public function writeChannelControl(MetadataEntities\Actions\IActionChannelControlEntity $action): void
	{
		$control = $this->channelControlsRepository->findById($action->getControl());

		if ($control === null) {
			$this->logger->debug(
				'Controlled control entity was not found in registry',
				[
					'source'  => Metadata\Constants::CONNECTOR_FB_MQTT_SOURCE,
					'type'    => 'client',
					'control' => [
						'device'  => $action->getDevice()->toString(),
						'channel' => $action->getChannel()->toString(),
						'control' => $action->getControl()->toString(),
					],
				]
			);

			return;
		}

		$channel = $this->channelsRepository->findById($action->getChannel());

		if ($channel === null) {
			$this->logger->debug(
				'Controlled channel entity was not found in registry',
				[
					'source'  => Metadata\Constants::CONNECTOR_FB_MQTT_SOURCE,
					'type'    => 'client',
					'control' => [
						'device'  => $action->getDevice()->toString(),
						'channel' => $action->getChannel()->toString(),
						'control' => $action->getControl()->toString(),
					],
				]
			);

			return;
		}

		$device = $this->devicesRepository->findById($action->getDevice());

		if ($device === null) {
			$this->logger->debug(
				'Controlled device entity was not found in registry',
				[
					'source'  => Metadata\Constants::CONNECTOR_FB_MQTT_SOURCE,
					'type'    => 'client',
					'control' => [
						'device'  => $action->getDevice()->toString(),
						'channel' => $action->getChannel()->toString(),
						'control' => $action->getControl()->toString(),
					],
				]
			);

			return;
		}

		$this->publish(
			$this->apiBuilder->buildChannelCommandTopic($device, $channel, $control),
			$action->getExpectedValue() !== null ? strval($action->getExpectedValue()) : null
		);
	}

	/**
	 * {@inheritDoc}
	 */
	protected function handleCommunication(): void
	{
		foreach ($this->processedProperties as $index => $processedProperty) {
			if (((float) $this->dateTimeFactory->getNow()->format('Uv') - (float) $processedProperty->format('Uv')) >= 500) {
				unset($this->processedProperties[$index]);
			}
		}

		foreach ($this->devicesRepository->findAllByConnector($this->connector->getId()) as $device) {
			if (
				!in_array($device->getId()->toString(), $this->processedDevices, true)
				&& $this->deviceConnectionStateManager->getState($device)->equalsValue(MetadataTypes\ConnectionStateType::STATE_READY)
			) {
				$this->processedDevices[] = $device->getId()->toString();

				if ($this->processDevice($device)) {
					return;
				}
			}
		}

		$this->processedDevices = [];
	}

	/**
	 * {@inheritDoc}
	 */
	protected function onClose(Mqtt\Connection $connection): void
	{
		parent::onClose($connection);

		foreach ($this->devicesRepository->findAllByConnector($this->connector->getId()) as $device) {
			if ($this->deviceConnectionStateManager->getState($device)->equalsValue(MetadataTypes\ConnectionStateType::STATE_READY)) {
				$this->deviceConnectionStateManager->setState(
					$device,
					MetadataTypes\ConnectionStateType::get(MetadataTypes\ConnectionStateType::STATE_DISCONNECTED)
				);
			}
		}
	}

	/**
	 * {@inheritDoc}
	 */
	protected function onConnect(Mqtt\Connection $connection): void
	{
		parent::onConnect($connection);

		$systemTopic = new Mqtt\DefaultSubscription(self::MQTT_SYSTEM_TOPIC);

		// Subscribe to system topic
		$this
			->subscribe($systemTopic)
			->done(
				function (Mqtt\Subscription $subscription): void {
					$this->logger->info(
						sprintf('Subscribed to: %s', $subscription->getFilter()),
						[
							'source' => Metadata\Constants::CONNECTOR_FB_MQTT_SOURCE,
							'type'   => 'client',
						]
					);
				},
				function (Throwable $ex): void {
					$this->logger->error(
						$ex->getMessage(),
						[
							'source'    => Metadata\Constants::CONNECTOR_FB_MQTT_SOURCE,
							'type'      => 'client',
							'exception' => [
								'message' => $ex->getMessage(),
								'code'    => $ex->getCode(),
							],
						]
					);
				}
			);

		// Get all device topics...
		foreach (self::DEVICES_TOPICS as $topic) {
			$topic = new Mqtt\DefaultSubscription($topic);

			// ...& subscribe to them
			$this
				->subscribe($topic)
				->done(
					function (Mqtt\Subscription $subscription): void {
						$this->logger->info(
							sprintf('Subscribed to: %s', $subscription->getFilter()),
							[
								'source' => Metadata\Constants::CONNECTOR_FB_MQTT_SOURCE,
								'type'   => 'client',
							]
						);
					},
					function (Throwable $ex): void {
						$this->logger->error(
							$ex->getMessage(),
							[
								'source'    => Metadata\Constants::CONNECTOR_FB_MQTT_SOURCE,
								'type'      => 'client',
								'exception' => [
									'message' => $ex->getMessage(),
									'code'    => $ex->getCode(),
								],
							]
						);
					}
				);
		}
	}

	/**
	 * {@inheritDoc}
	 */
	protected function onMessage(Mqtt\Message $message): void
	{
		parent::onMessage($message);

		// Check for broker system topic
		if (str_contains($message->getTopic(), '$SYS')) {
			[ , $param1, $param2, $param3] = explode(FbMqttConnector\Constants::MQTT_TOPIC_DELIMITER, $message->getTopic()) + [null, null, null, null];

			$payload = $message->getPayload();

			// Broker log
			if ($param1 === 'broker' && $param2 === 'log') {
				switch ($param3) {
					// Notice
					case 'N':
						$this->logger->notice(
							$payload,
							[
								'source' => Metadata\Constants::CONNECTOR_FB_MQTT_SOURCE,
								'type'   => 'client',
							]
						);

						// Nev device connected message
						if (str_contains($message->getPayload(), self::NEW_CLIENT_MESSAGE_PAYLOAD)) {
							[ , , , , , $ipAddress, , $deviceId, , , $username] = explode(' ', $message->getPayload()) + [null, null, null, null, null, null, null, null, null, null, null];

							// Check for correct data
							if ($username !== null && $deviceId !== null && $ipAddress !== null) {
								$entity = new Entities\Messages\DevicePropertyEntity(
									$this->connector->getId(),
									$deviceId,
									'ip-address'
								);
								$entity->setValue($ipAddress);

								$this->consumer->append($entity);
							}
						}

						break;

					// Error
					case 'E':
						$this->logger->error(
							$payload,
							[
								'source' => Metadata\Constants::CONNECTOR_FB_MQTT_SOURCE,
								'type'   => 'client',
							]
						);
						break;

					// Information
					case 'I':
						$this->logger->info(
							$payload,
							[
								'source' => Metadata\Constants::CONNECTOR_FB_MQTT_SOURCE,
								'type'   => 'client',
							]
						);
						break;

					default:
						$this->logger->debug(
							$param3 . ': ' . $payload,
							[
								'source' => Metadata\Constants::CONNECTOR_FB_MQTT_SOURCE,
								'type'   => 'client',
							]
						);
						break;
				}
			}

			return;
		}

		// Connected device topic
		if (
			$this->apiValidator->validateConvention($message->getTopic())
			&& $this->apiValidator->validateVersion($message->getTopic())
		) {
			// Check if message is sent from broker
			if (!$this->apiValidator->validate($message->getTopic())) {
				return;
			}

			try {
				$entity = $this->apiParser->parse(
					$this->connector->getId(),
					$message->getTopic(),
					$message->getPayload(),
					$message->isRetained()
				);

			} catch (Exceptions\ParseMessageException $ex) {
				$this->logger->debug(
					'Received message could not be successfully parsed to entity',
					[
						'source'    => Metadata\Constants::CONNECTOR_FB_MQTT_SOURCE,
						'type'      => 'client',
						'exception' => [
							'message' => $ex->getMessage(),
							'code'    => $ex->getCode(),
						],
					]
				);

				return;
			}

			$this->consumer->append($entity);
		}
	}

	/**
	 * @param MetadataEntities\Modules\DevicesModule\IDeviceEntity $device
	 *
	 * @return bool
	 */
	private function processDevice(MetadataEntities\Modules\DevicesModule\IDeviceEntity $device): bool
	{
		if ($this->writeDeviceProperty($device)) {
			return true;
		}

		return $this->writeChannelsProperty($device);
	}

	/**
	 * @param MetadataEntities\Modules\DevicesModule\IDeviceEntity $device
	 *
	 * @return bool
	 */
	private function writeDeviceProperty(MetadataEntities\Modules\DevicesModule\IDeviceEntity $device): bool
	{
		$now = $this->dateTimeFactory->getNow();

		foreach ($this->devicePropertiesRepository->findAllByDevice($device->getId()) as $property) {
			if (
				(
					$property instanceof MetadataEntities\Modules\DevicesModule\IDeviceDynamicPropertyEntity
					|| $property instanceof MetadataEntities\Modules\DevicesModule\IDeviceMappedPropertyEntity
				)
				&& $property->getExpectedValue() !== null
				&& $property->isPending()
			) {
				$pending = is_string($property->getPending()) ? Utils\DateTime::createFromFormat(DateTimeInterface::ATOM, $property->getPending()) : true;
				$debounce = array_key_exists($property->getId()->toString(), $this->processedProperties) ? $this->processedProperties[$property->getId()->toString()] : false;

				if (
					$debounce !== false
					&& (float) $now->format('Uv') - (float) $debounce->format('Uv') < 500
				) {
					continue;
				}

				unset($this->processedProperties[$property->getId()->toString()]);

				if (
					$pending === true
					|| (
						$pending !== false
						&& (float) $now->format('Uv') - (float) $pending->format('Uv') > 2000
					)
				) {
					$this->processedProperties[$property->getId()->toString()] = $now;

					$this->publish(
						$this->apiBuilder->buildDevicePropertyTopic($device, $property),
						strval($property->getExpectedValue())
					)->then(function () use ($property, $now): void {
						$state = $this->devicePropertiesStatesRepository->findOne($property);

						if ($state !== null) {
							$this->devicePropertiesStatesManager->update(
								$property,
								$state,
								Utils\ArrayHash::from([
									'pending' => $now->format(DateTimeInterface::ATOM),
								])
							);
						}
					})->otherwise(function () use ($property): void {
						unset($this->processedProperties[$property->getId()->toString()]);
					});

					return true;
				}
			}
		}

		return false;
	}

	/**
	 * @param MetadataEntities\Modules\DevicesModule\IDeviceEntity $device
	 *
	 * @return bool
	 */
	private function writeChannelsProperty(MetadataEntities\Modules\DevicesModule\IDeviceEntity $device): bool
	{
		$now = $this->dateTimeFactory->getNow();

		foreach ($this->channelsRepository->findAllByDevice($device->getId()) as $channel) {
			foreach ($this->channelPropertiesRepository->findAllByChannel($channel->getId()) as $property) {
				if (
					(
						$property instanceof MetadataEntities\Modules\DevicesModule\IChannelDynamicPropertyEntity
						|| $property instanceof MetadataEntities\Modules\DevicesModule\IChannelMappedPropertyEntity
					)
					&& $property->getExpectedValue() !== null
					&& $property->isPending()
				) {
					$pending = is_string($property->getPending()) ? Utils\DateTime::createFromFormat(DateTimeInterface::ATOM, $property->getPending()) : true;
					$debounce = array_key_exists($property->getId()->toString(), $this->processedProperties) ? $this->processedProperties[$property->getId()->toString()] : false;

					if (
						$debounce !== false
						&& (float) $now->format('Uv') - (float) $debounce->format('Uv') < 500
					) {
						continue;
					}

					unset($this->processedProperties[$property->getId()->toString()]);

					if (
						$pending === true
						|| (
							$pending !== false
							&& (float) $now->format('Uv') - (float) $pending->format('Uv') > 2000
						)
					) {
						$this->processedProperties[$property->getId()->toString()] = $now;

						$this->publish(
							$this->apiBuilder->buildChannelPropertyTopic($device, $channel, $property),
							strval($property->getExpectedValue())
						)->then(function () use ($property, $now): void {
							$state = $this->channelPropertiesStatesRepository->findOne($property);

							if ($state !== null) {
								$this->channelPropertiesStatesManager->update(
									$property,
									$state,
									Utils\ArrayHash::from([
										'pending' => $now->format(DateTimeInterface::ATOM),
									])
								);
							}
						})->otherwise(function () use ($property): void {
							unset($this->processedProperties[$property->getId()->toString()]);
						});

						return true;
					}
				}
			}
		}

		return false;
	}

}