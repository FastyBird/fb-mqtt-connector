<?php declare(strict_types = 1);

/**
 * FbMqttConnectorHydrator.php
 *
 * @license        More in LICENSE.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:FbMqttConnector!
 * @subpackage     Hydrators
 * @since          0.4.0
 *
 * @date           07.12.21
 */

namespace FastyBird\FbMqttConnector\Hydrators;

use FastyBird\DevicesModule\Hydrators as DevicesModuleHydrators;
use FastyBird\FbMqttConnector\Entities;

/**
 * FastyBird MQTT Connector entity hydrator
 *
 * @package        FastyBird:FbMqttConnector!
 * @subpackage     Hydrators
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 *
 * @phpstan-extends DevicesModuleHydrators\Connectors\ConnectorHydrator<Entities\IFbMqttConnectorEntity>
 */
final class FbMqttConnectorHydrator extends DevicesModuleHydrators\Connectors\ConnectorHydrator
{

	/**
	 * {@inheritDoc}
	 */
	public function getEntityName(): string
	{
		return Entities\FbMqttConnectorEntity::class;
	}

}
