<?php declare(strict_types = 1);

/**
 * IConsumer.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:MqttConnectorPlugin!
 * @subpackage     Consumers
 * @since          0.1.0
 *
 * @date           08.03.20
 */

namespace FastyBird\MqttConnectorPlugin\Consumers;

use FastyBird\MqttConnectorPlugin\Entities;

/**
 * Exchange messages consumer interface
 *
 * @package        FastyBird:MqttConnectorPlugin!
 * @subpackage     Consumers
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface IConsumer
{

	/**
	 * @param Entities\IEntity $entity
	 *
	 * @return void
	 */
	public function consume(Entities\IEntity $entity): void;

}
