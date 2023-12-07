<?php declare(strict_types = 1);

/**
 * ConnectionManager.php
 *
 * @license        More in LICENSE.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:FbMqttConnector!
 * @subpackage     API
 * @since          1.0.0
 *
 * @date           03.12.23
 */

namespace FastyBird\Connector\FbMqtt\API;

use FastyBird\Connector\FbMqtt\API;
use FastyBird\Connector\FbMqtt\Helpers;
use FastyBird\Library\Metadata\Documents as MetadataDocuments;
use FastyBird\Library\Metadata\Exceptions as MetadataExceptions;
use FastyBird\Module\Devices\Exceptions as DevicesExceptions;
use Nette;

/**
 * Client connections manager
 *
 * @package        FastyBird:FbMqttConnector!
 * @subpackage     API
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class ConnectionManager
{

	use Nette\SmartObject;

	private Client|null $clientConnection = null;

	public function __construct(
		private readonly ClientFactory $clientFactory,
		private readonly Helpers\Connector $connectorHelper,
	)
	{
	}

	/**
	 * @throws DevicesExceptions\InvalidState
	 * @throws MetadataExceptions\InvalidArgument
	 * @throws MetadataExceptions\InvalidState
	 */
	public function getConnection(MetadataDocuments\DevicesModule\Connector $connector): Client
	{
		if ($this->clientConnection === null) {
			$this->clientConnection = $this->clientFactory->create(
				$connector->getId()->toString(),
				$this->connectorHelper->getServerAddress($connector),
				$this->connectorHelper->getServerPort($connector),
				$this->connectorHelper->getUsername($connector),
				$this->connectorHelper->getPassword($connector),
			);
		}

		return $this->clientConnection;
	}

	public function __destruct()
	{
		$this->clientConnection?->disconnect();
	}

}
