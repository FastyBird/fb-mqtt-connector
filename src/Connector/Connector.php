<?php declare(strict_types = 1);

/**
 * Constants.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:FbMqttConnector!
 * @subpackage     Connector
 * @since          0.25.0
 *
 * @date           22.06.22
 */

namespace FastyBird\FbMqttConnector\Connector;

use FastyBird\DevicesModule\Connectors as DevicesModuleConnectors;
use FastyBird\FbMqttConnector\Client;
use FastyBird\Metadata\Entities as MetadataEntities;
use FastyBird\Metadata\Types as MetadataTypes;

/**
 * Service constants
 *
 * @package        FastyBird:FbMqttConnector!
 * @subpackage     Connector
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class Connector implements DevicesModuleConnectors\IConnector
{

	/** @var Client\IClient */
	private Client\IClient $client;

	public function __construct(
		Client\IClient $client
	) {
		$this->client = $client;
	}

	/**
	 * {@inheritDoc}
	 */
	public function execute(): void
	{
		$this->client->connect();
	}

	/**
	 * {@inheritDoc}
	 */
	public function terminate(): void
	{
		$this->client->disconnect();
	}

	/**
	 * {@inheritDoc}
	 */
	public function hasUnfinishedTasks(): bool
	{
		return false;
	}

	/**
	 * @param MetadataEntities\Actions\IActionDeviceControlEntity $action
	 *
	 * @return void
	 */
	public function handleDeviceControlAction(MetadataEntities\Actions\IActionDeviceControlEntity $action): void
	{
		if (!$action->getAction()->equalsValue(MetadataTypes\ControlActionType::ACTION_SET)) {
			return;
		}

		$this->client->writeDeviceControl($action);
	}

	/**
	 * @param MetadataEntities\Actions\IActionChannelControlEntity $action
	 *
	 * @return void
	 */
	public function handleChannelControlAction(MetadataEntities\Actions\IActionChannelControlEntity $action): void
	{
		if (!$action->getAction()->equalsValue(MetadataTypes\ControlActionType::ACTION_SET)) {
			return;
		}

		$this->client->writeChannelControl($action);
	}

}
