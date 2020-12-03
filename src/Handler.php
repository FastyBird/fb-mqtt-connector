<?php declare(strict_types = 1);

/**
 * Handler.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:MqttPlugin!
 * @subpackage     common
 * @since          0.1.0
 *
 * @date           23.02.20
 */

namespace FastyBird\MqttPlugin;

use Closure;
use Nette;

/**
 * MQTT client handler
 *
 * @package        FastyBird:MqttPlugin!
 * @subpackage     common
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 *
 * @method onMessage(Entities\IEntity $entity)
 */
final class Handler
{

	use Nette\SmartObject;

	/** @var Closure[] */
	public $onMessage = [];

	/**
	 * @param Entities\IEntity $entity
	 *
	 * @return void
	 */
	public function handleMessage(Entities\IEntity $entity): void
	{
		$this->onMessage($entity);
	}

}
