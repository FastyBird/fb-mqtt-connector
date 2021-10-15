<?php declare(strict_types = 1);

/**
 * Consumer.php
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
use Nette;
use SplObjectStorage;

/**
 * Exchange message consumer container
 *
 * @package        FastyBird:MqttConnectorPlugin!
 * @subpackage     Consumers
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class Consumer implements IConsumer
{

	use Nette\SmartObject;

	/** @var SplObjectStorage<IConsumer, null> */
	private SplObjectStorage $consumers;

	public function __construct()
	{
		$this->consumers = new SplObjectStorage();
	}

	/**
	 * @param IConsumer $consumer
	 *
	 * @return void
	 */
	public function addConsumer(IConsumer $consumer): void
	{
		$this->consumers->attach($consumer);
	}

	/**
	 * {@inheritDoc}
	 */
	public function consume(Entities\IEntity $entity): void
	{
		$this->consumers->rewind();

		/** @var IConsumer $consumer */
		foreach ($this->consumers as $consumer) {
			$consumer->consume($entity);
		}
	}

}
