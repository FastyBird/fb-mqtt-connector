<?php declare(strict_types = 1);

/**
 * IFbMqttDevice.php
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
 * FastyBird MQTT device entity interface
 *
 * @package        FastyBird:FbMqttConnector!
 * @subpackage     Entities
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface IFbMqttDevice extends DevicesModuleEntities\Devices\IDevice
{

}
