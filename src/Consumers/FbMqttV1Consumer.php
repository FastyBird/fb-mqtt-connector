<?php declare(strict_types = 1);

/**
 * FbMqttV1Consumer.php
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

use FastyBird\DevicesModule\Models as DevicesModuleModels;
use FastyBird\DevicesModule\Queries as  DevicesModuleQueries;
use FastyBird\Exchange as FastyBirdExchange;
use FastyBird\FbMqttConnector\API;
use FastyBird\FbMqttConnector\Client;
use FastyBird\Metadata\Types as MetadataTypes;
use Nette;
use Nette\Utils;
use Ramsey\Uuid;

/**
 * Exchange messages consumer
 *
 * @package        FastyBird:FbMqttConnector!
 * @subpackage     Consumers
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
class FbMqttV1Consumer implements FastyBirdExchange\Consumer\IConsumer
{

	use Nette\SmartObject;

	/** @var DevicesModuleModels\Devices\Properties\IPropertiesRepository */
	private DevicesModuleModels\Devices\Properties\IPropertiesRepository $devicesPropertiesRepository;

	/** @var DevicesModuleModels\Devices\Controls\IControlsRepository */
	private DevicesModuleModels\Devices\Controls\IControlsRepository $devicesControlsRepository;

	/** @var DevicesModuleModels\Channels\Properties\IPropertiesRepository */
	private DevicesModuleModels\Channels\Properties\IPropertiesRepository $channelsPropertiesRepository;

	/** @var DevicesModuleModels\Channels\Controls\IControlsRepository */
	private DevicesModuleModels\Channels\Controls\IControlsRepository $channelsControlsRepository;

	/** @var API\V1Builder */
	private API\V1Builder $apiBuilder;

	/** @var Client\IClient|null */
	private ?Client\IClient $client;

	public function __construct(
		API\V1Builder $apiBuilder,
		DevicesModuleModels\Devices\Properties\IPropertiesRepository $devicesPropertiesRepository,
		DevicesModuleModels\Devices\Controls\IControlsRepository $devicesControlsRepository,
		DevicesModuleModels\Channels\Properties\IPropertiesRepository $channelsPropertiesRepository,
		DevicesModuleModels\Channels\Controls\IControlsRepository $channelsControlsRepository
	) {
		$this->apiBuilder = $apiBuilder;

		$this->devicesPropertiesRepository = $devicesPropertiesRepository;
		$this->devicesControlsRepository = $devicesControlsRepository;
		$this->channelsPropertiesRepository = $channelsPropertiesRepository;
		$this->channelsControlsRepository = $channelsControlsRepository;
	}

	/**
	 * @param Client\IClient|null $client
	 */
	public function setClient(?Client\IClient $client): void
	{
		$this->client = $client;
	}

	/**
	 * {@inheritDoc}
	 */
	public function consume($origin, MetadataTypes\RoutingKeyType $routingKey, ?Utils\ArrayHash $data): void
	{
		if ($this->client === null || $data === null) {
			return;
		}

		if ($routingKey->equalsValue(MetadataTypes\RoutingKeyType::ROUTE_DEVICE_ACTION)) {
			if ($data->offsetGet('action') !== MetadataTypes\ControlActionType::ACTION_SET) {
				return;
			}

			$findControlQuery = new DevicesModuleQueries\FindDeviceControlsQuery();
			$findControlQuery->byId(Uuid\Uuid::fromString($data->offsetGet('control')));

			$control = $this->devicesControlsRepository->findOneBy($findControlQuery);

			if ($control === null) {
				return;
			}

			$this->client->publish(
				$this->apiBuilder->buildDeviceCommandTopic(
					$control->getDevice()->getIdentifier(),
					$control->getName(),
					$control->getDevice()->getParent() !== null ? $control->getDevice()->getParent()->getIdentifier() : null
				),
				$data->offsetGet('expected_value')
			);

		} elseif ($routingKey->equalsValue(MetadataTypes\RoutingKeyType::ROUTE_DEVICE_PROPERTY_ACTION)) {
			if ($data->offsetGet('action') !== MetadataTypes\PropertyActionType::ACTION_SET) {
				return;
			}

			$findPropertyQuery = new DevicesModuleQueries\FindDevicePropertiesQuery();
			$findPropertyQuery->byId(Uuid\Uuid::fromString($data->offsetGet('property')));

			$property = $this->devicesPropertiesRepository->findOneBy($findPropertyQuery);

			if ($property === null) {
				return;
			}

			$this->client->publish(
				$this->apiBuilder->buildDevicePropertyTopic(
					$property->getDevice()->getIdentifier(),
					$property->getIdentifier(),
					$property->getDevice()->getParent() !== null ? $property->getDevice()->getParent()->getIdentifier() : null
				),
				$data->offsetGet('expected_value')
			);

		} elseif ($routingKey->equalsValue(MetadataTypes\RoutingKeyType::ROUTE_CHANNEL_ACTION)) {
			if ($data->offsetGet('action') !== MetadataTypes\ControlActionType::ACTION_SET) {
				return;
			}

			$findControlQuery = new DevicesModuleQueries\FindChannelControlsQuery();
			$findControlQuery->byId(Uuid\Uuid::fromString($data->offsetGet('control')));

			$control = $this->channelsControlsRepository->findOneBy($findControlQuery);

			if ($control === null) {
				return;
			}

			$this->client->publish(
				$this->apiBuilder->buildChannelCommandTopic(
					$control->getChannel()->getDevice()->getIdentifier(),
					$control->getChannel()->getIdentifier(),
					$control->getName(),
					$control->getChannel()->getDevice()->getParent() !== null ? $control->getChannel()->getDevice()->getParent()->getIdentifier() : null
				),
				$data->offsetGet('expected_value')
			);

		} elseif ($routingKey->equalsValue(MetadataTypes\RoutingKeyType::ROUTE_CHANNEL_PROPERTY_ACTION)) {
			if ($data->offsetGet('action') !== MetadataTypes\PropertyActionType::ACTION_SET) {
				return;
			}

			$findPropertyQuery = new DevicesModuleQueries\FindChannelPropertiesQuery();
			$findPropertyQuery->byId(Uuid\Uuid::fromString($data->offsetGet('property')));

			$property = $this->channelsPropertiesRepository->findOneBy($findPropertyQuery);

			if ($property === null) {
				return;
			}

			$this->client->publish(
				$this->apiBuilder->buildChannelPropertyTopic(
					$property->getChannel()->getDevice()->getIdentifier(),
					$property->getChannel()->getIdentifier(),
					$property->getIdentifier(),
					$property->getChannel()->getDevice()->getParent() !== null ? $property->getChannel()->getDevice()->getParent()->getIdentifier() : null
				),
				$data->offsetGet('expected_value')
			);
		}
	}

}
