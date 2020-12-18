<?php declare(strict_types = 1);

/**
 * IExchangeConsumer.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:MqttPlugin!
 * @subpackage     Consumers
 * @since          0.1.0
 *
 * @date           08.03.20
 */

namespace FastyBird\MqttPlugin\Consumers;

use FastyBird\MqttPlugin\Entities;

/**
 * Exchange messages consumer interface
 *
 * @package        FastyBird:MqttPlugin!
 * @subpackage     Consumers
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface IExchangeConsumer
{

	/**
	 * @param IMessageHandler $handler
	 *
	 * @return void
	 */
	public function addHandler(IMessageHandler $handler): void;

	/**
	 * @return bool
	 */
	public function hasHandlers(): bool;

	/**
	 * @param Entities\IEntity $entity
	 *
	 * @return void
	 */
	public function consume(Entities\IEntity $entity): void;

}
