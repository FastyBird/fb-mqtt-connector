<?php declare(strict_types = 1);

/**
 * ExchangeConsumer.php
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
use Nette;
use SplObjectStorage;
use Throwable;

/**
 * Exchange message consumer container
 *
 * @package        FastyBird:MqttPlugin!
 * @subpackage     Consumers
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class ExchangeConsumer implements IExchangeConsumer
{

	use Nette\SmartObject;

	/** @var SplObjectStorage */
	private SplObjectStorage $handlers;

	public function __construct()
	{
		$this->handlers = new SplObjectStorage();
	}

	/**
	 * {@inheritDoc}
	 */
	public function addHandler(IMessageHandler $handler): void
	{
		$this->handlers->attach($handler);
	}

	/**
	 * {@inheritDoc}
	 */
	public function hasHandlers(): bool
	{
		return $this->handlers->count() > 0;
	}

	/**
	 * {@inheritDoc}
	 */
	public function consume(Entities\IEntity $entity): void
	{
		/** @var IMessageHandler $handler */
		foreach ($this->handlers as $handler) {
			$this->processMessage($entity, $handler);
		}
	}

	/**
	 * @param Entities\IEntity $entity
	 * @param IMessageHandler $handler
	 *
	 * @return bool
	 */
	private function processMessage(
		Entities\IEntity $entity,
		IMessageHandler $handler
	): bool {
		try {
			return $handler->process($entity);

		} catch (Throwable $ex) {
			return false;
		}
	}

}
