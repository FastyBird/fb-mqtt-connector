<?php declare(strict_types = 1);

/**
 * FbMqttV1Client.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:FbMqttConnector!
 * @subpackage     Client
 * @since          0.1.0
 *
 * @date           23.02.20
 */

namespace FastyBird\FbMqttConnector\Client;

use BinSoul\Net\Mqtt;
use FastyBird\DevicesModule\Models as DevicesModuleModels;
use FastyBird\FbMqttConnector;
use FastyBird\FbMqttConnector\API;
use FastyBird\FbMqttConnector\Consumers;
use FastyBird\FbMqttConnector\Entities;
use FastyBird\FbMqttConnector\Exceptions;
use FastyBird\FbMqttConnector\Types;
use FastyBird\Metadata\Entities as MetadataEntities;
use React\EventLoop;
use Throwable;

/**
 * FastyBird MQTT v1 client
 *
 * @package        FastyBird:FbMqttConnector!
 * @subpackage     Client
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

	/** @var API\V1Validator */
	private API\V1Validator $apiValidator;

	/** @var API\V1Parser */
	private API\V1Parser $apiParser;

	/** @var API\V1Builder */
	private API\V1Builder $apiBuilder;

	/** @var Consumers\IConsumer */
	private Consumers\IConsumer $consumer;

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

	public function __construct(
		MetadataEntities\Modules\DevicesModule\IConnectorEntity $connector,
		API\V1Validator $apiValidator,
		API\V1Parser $apiParser,
		API\V1Builder $apiBuilder,
		Consumers\IConsumer $consumer,
		DevicesModuleModels\DataStorage\IConnectorPropertiesRepository $connectorPropertiesRepository,
		DevicesModuleModels\DataStorage\IDevicesRepository $devicesRepository,
		DevicesModuleModels\DataStorage\IDevicePropertiesRepository $devicePropertiesRepository,
		DevicesModuleModels\DataStorage\IDeviceControlsRepository $deviceControlsRepository,
		DevicesModuleModels\DataStorage\IChannelsRepository $channelsRepository,
		DevicesModuleModels\DataStorage\IChannelPropertiesRepository $channelPropertiesRepository,
		DevicesModuleModels\DataStorage\IChannelControlsRepository $channelControlsRepository,
		EventLoop\LoopInterface $loop,
		?Mqtt\ClientIdentifierGenerator $identifierGenerator = null,
		?Mqtt\FlowFactory $flowFactory = null,
		?Mqtt\StreamParser $parser = null
	) {
		parent::__construct($connector, $connectorPropertiesRepository, $loop, $identifierGenerator, $flowFactory, $parser);

		$this->apiValidator = $apiValidator;
		$this->apiParser = $apiParser;
		$this->apiBuilder = $apiBuilder;
		$this->consumer = $consumer;

		$this->devicesRepository = $devicesRepository;
		$this->devicePropertiesRepository = $devicePropertiesRepository;
		$this->deviceControlsRepository = $deviceControlsRepository;
		$this->channelsRepository = $channelsRepository;
		$this->channelPropertiesRepository = $channelPropertiesRepository;
		$this->channelControlsRepository = $channelControlsRepository;
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
	public function writeDeviceControl(MetadataEntities\Actions\IActionDeviceEntity $action): void
	{
		$control = $this->deviceControlsRepository->findById($action->getControl());

		if ($control === null) {
			$this->logger->debug(
				'Controlled device control entity was not found in registry',
				[
					'source'  => 'fastybird-fb-mqtt-connector',
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
					'source'  => 'fastybird-fb-mqtt-connector',
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
	public function writeChannelControl(MetadataEntities\Actions\IActionChannelEntity $action): void
	{
		$control = $this->channelControlsRepository->findById($action->getControl());

		if ($control === null) {
			$this->logger->debug(
				'Controlled control entity was not found in registry',
				[
					'source'  => 'fastybird-fb-mqtt-connector',
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
					'source'  => 'fastybird-fb-mqtt-connector',
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
					'source'  => 'fastybird-fb-mqtt-connector',
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
		foreach ($this->devicesRepository->findAllByConnector($this->connector->getId()) as $device) {
			if (!in_array($device->getId()->toString(), $this->processedDevices, true)) {
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
							'source'    => 'fastybird-fb-mqtt-connector',
							'type'      => 'client',
						]
					);
				},
				function (Throwable $ex): void {
					$this->logger->error(
						$ex->getMessage(),
						[
							'source'    => 'fastybird-fb-mqtt-connector',
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
								'source'    => 'fastybird-fb-mqtt-connector',
								'type'      => 'client',
							]
						);
					},
					function (Throwable $ex): void {
						$this->logger->error(
							$ex->getMessage(),
							[
								'source'    => 'fastybird-fb-mqtt-connector',
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
		if (strpos($message->getTopic(), '$SYS') !== false) {
			[, $param1, $param2, $param3] = explode(FbMqttConnector\Constants::MQTT_TOPIC_DELIMITER, $message->getTopic()) + [null, null, null, null];

			$payload = $message->getPayload();

			// Broker log
			if ($param1 === 'broker' && $param2 === 'log') {
				switch ($param3) {
					// Notice
					case 'N':
						$this->logger->notice(
							$payload,
							[
								'source'    => 'fastybird-fb-mqtt-connector',
								'type'      => 'client',
							]
						);

						// Nev device connected message
						if (strpos($message->getPayload(), self::NEW_CLIENT_MESSAGE_PAYLOAD) !== false) {
							[, , , , , $ipAddress, , $deviceId, , , $username] = explode(' ', $message->getPayload()) + [null, null, null, null, null, null, null, null, null, null, null];

							// Check for correct data
							if ($username !== null && $deviceId !== null && $ipAddress !== null) {
								$entity = new Entities\Messages\DeviceProperty(
									$deviceId,
									'ip-address'
								);
								$entity->setValue($ipAddress);

								$this->consumer->consume($entity);
							}
						}

						break;

					// Error
					case 'E':
						$this->logger->error(
							$payload,
							[
								'source'    => 'fastybird-fb-mqtt-connector',
								'type'      => 'client',
							]
						);
						break;

					// Information
					case 'I':
						$this->logger->info(
							$payload,
							[
								'source'    => 'fastybird-fb-mqtt-connector',
								'type'      => 'client',
							]
						);
						break;

					default:
						$this->logger->debug(
							$param3 . ': ' . $payload,
							[
								'source'    => 'fastybird-fb-mqtt-connector',
								'type'      => 'client',
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
					$message->getTopic(),
					$message->getPayload(),
					$message->isRetained()
				);

			} catch (Exceptions\ParseMessageException $ex) {
				$this->logger->debug(
					'Received message could not be successfully parsed to entity.',
					[
						'source'    => 'fastybird-fb-mqtt-connector',
						'type'      => 'client',
						'exception' => [
							'message' => $ex->getMessage(),
							'code'    => $ex->getCode(),
						],
					]
				);

				return;
			}

			$this->consumer->consume($entity);
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
		foreach ($this->devicePropertiesRepository->findAllByDevice($device->getId()) as $property) {
			if (
				(
					$property instanceof MetadataEntities\Modules\DevicesModule\IDeviceDynamicPropertyEntity
					|| $property instanceof MetadataEntities\Modules\DevicesModule\IDeviceMappedPropertyEntity
				)
				&& $property->getExpectedValue() !== null
			) {
				$this->publish(
					$this->apiBuilder->buildDevicePropertyTopic($device, $property),
					strval($property->getExpectedValue())
				);

				return true;
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
		foreach ($this->channelsRepository->findAllByDevice($device->getId()) as $channel) {
			foreach ($this->channelPropertiesRepository->findAllByChannel($channel->getId()) as $property) {
				if (
					(
						$property instanceof MetadataEntities\Modules\DevicesModule\IChannelDynamicPropertyEntity
						|| $property instanceof MetadataEntities\Modules\DevicesModule\IChannelMappedPropertyEntity
					)
					&& $property->getExpectedValue() !== null
				) {
					$this->publish(
						$this->apiBuilder->buildChannelPropertyTopic($device, $channel, $property),
						strval($property->getExpectedValue())
					);

					return true;
				}
			}
		}

		return false;
	}

}
