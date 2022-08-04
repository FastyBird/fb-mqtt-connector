<?php declare(strict_types = 1);

/**
 * IFbMqttConnectorEntity.php
 *
 * @license        More in LICENSE.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:FbMqttConnector!
 * @subpackage     Entities
 * @since          0.4.0
 *
 * @date           05.02.22
 */

namespace FastyBird\FbMqttConnector\Entities;

use FastyBird\DevicesModule\Entities as DevicesModuleEntities;

/**
 * FastyBird MQTT connector entity interface
 *
 * @package        FastyBird:FbMqttConnector!
 * @subpackage     Entities
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface IFbMqttConnectorEntity extends DevicesModuleEntities\Connectors\IConnector
{

}
