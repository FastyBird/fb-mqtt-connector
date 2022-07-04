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
use Psr\Log;
use Ramsey\Uuid;

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

	/** @var bool */
	private bool $stopped = true;

	/** @var Client\IClient */
	private Client\IClient $client;

	/** @var MetadataEntities\Modules\DevicesModule\IConnectorEntity */
	private MetadataEntities\Modules\DevicesModule\IConnectorEntity $connector;

	/** @var Log\LoggerInterface */
	private Log\LoggerInterface $logger;

	public function __construct(
		MetadataEntities\Modules\DevicesModule\IConnectorEntity $connector,
		Client\IClient $client,
		?Log\LoggerInterface $logger = null
	) {
		$this->connector = $connector;

		$this->client = $client;

		$this->logger = $logger ?? new Log\NullLogger();
	}

	/**
	 * {@inheritDoc}
	 */
	public function getId(): Uuid\UuidInterface
	{
		return $this->connector->getId();
	}

	/**
	 * {@inheritDoc}
	 */
	public function execute(): void
	{
		$this->client->connect($this->connector);
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
	 * @param MetadataEntities\Actions\IActionDeviceEntity|MetadataEntities\Actions\IActionChannelEntity $action
	 *
	 * @return void
	 */
	public function handleControlAction($action): void
	{
		if ($action instanceof MetadataEntities\Actions\IActionDeviceEntity) {
			if (!$action->getAction()->equalsValue(MetadataTypes\ControlActionType::ACTION_SET)) {
				return;
			}

			$this->client->writeDeviceControl($action);

		} elseif ($action instanceof MetadataEntities\Actions\IActionChannelEntity) {
			if (!$action->getAction()->equalsValue(MetadataTypes\ControlActionType::ACTION_SET)) {
				return;
			}

			$this->client->writeChannelControl($action);
		}
	}

}
