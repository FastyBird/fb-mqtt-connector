<?php declare(strict_types = 1);

/**
 * ConnectorFactory.php
 *
 * @license        More in LICENSE.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:FbMqttConnector!
 * @subpackage     Connector
 * @since          1.0.0
 *
 * @date           23.07.22
 */

namespace FastyBird\Connector\FbMqtt\Connector;

use FastyBird\Connector\FbMqtt\Connector;
use FastyBird\Module\Devices\Connectors as DevicesConnectors;
use FastyBird\Module\Devices\Documents as DevicesDocuments;

/**
 * Connector service executor factory
 *
 * @package        FastyBird:FbMqttConnector!
 * @subpackage     Connector
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface ConnectorFactory extends DevicesConnectors\ConnectorFactory
{

	public function create(
		DevicesDocuments\Connectors\Connector $connector,
	): Connector\Connector;

}
