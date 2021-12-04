<?php declare(strict_types = 1);

/**
 * ClientHandler.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:FbMqttConnectorPlugin!
 * @subpackage     Handlers
 * @since          0.1.0
 *
 * @date           15.10.21
 */

namespace FastyBird\MqttConnectorPlugin\Handlers;

use BinSoul\Net\Mqtt;
use FastyBird\MqttConnectorPlugin\Client;
use Nette;
use SplObjectStorage;
use Throwable;

/**
 * MQTT client handler proxy
 *
 * @package         FastyBird:FbMqttConnectorPlugin!
 * @subpackage      Handlers
 *
 * @author          Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class ClientHandler implements IHandler
{

	use Nette\SmartObject;

	/** @var SplObjectStorage<IHandler, null> */
	private SplObjectStorage $handlers;

	public function __construct()
	{
		$this->handlers = new SplObjectStorage();
	}

	/**
	 * @param IHandler $handler
	 */
	public function addHandler(IHandler $handler): void
	{
		$this->handlers->attach($handler);
	}

	/**
	 * {@inheritDoc}
	 */
	public function onOpen(Mqtt\Connection $connection, Client\MqttClient $client): void
	{
		/** @var IHandler $handler */
		foreach ($this->getHandlers() as $handler) {
			$handler->onOpen($connection, $client);
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function onClose(Mqtt\Connection $connection, Client\MqttClient $client): void
	{
		/** @var IHandler $handler */
		foreach ($this->getHandlers() as $handler) {
			$handler->onClose($connection, $client);
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function onConnect(Mqtt\Connection $connection, Client\MqttClient $client): void
	{
		/** @var IHandler $handler */
		foreach ($this->getHandlers() as $handler) {
			$handler->onConnect($connection, $client);
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function onDisconnect(Mqtt\Connection $connection, Client\MqttClient $client): void
	{
		/** @var IHandler $handler */
		foreach ($this->getHandlers() as $handler) {
			$handler->onDisconnect($connection, $client);
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function onWarning(Throwable $ex, Client\MqttClient $client): void
	{
		/** @var IHandler $handler */
		foreach ($this->getHandlers() as $handler) {
			$handler->onWarning($ex, $client);
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function onError(Throwable $ex, Client\MqttClient $client): void
	{
		/** @var IHandler $handler */
		foreach ($this->getHandlers() as $handler) {
			$handler->onError($ex, $client);
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function onMessage(Mqtt\Message $message, Client\MqttClient $client): void
	{
		/** @var IHandler $handler */
		foreach ($this->getHandlers() as $handler) {
			$handler->onMessage($message, $client);
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function onSubscribe(Mqtt\Subscription $subscription, Client\MqttClient $client): void
	{
		/** @var IHandler $handler */
		foreach ($this->getHandlers() as $handler) {
			$handler->onSubscribe($subscription, $client);
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function onUnsubscribe(array $subscriptions, Client\MqttClient $client): void
	{
		/** @var IHandler $handler */
		foreach ($this->getHandlers() as $handler) {
			$handler->onUnsubscribe($subscriptions, $client);
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function onPublish(Mqtt\Message $message, Client\MqttClient $client): void
	{
		/** @var IHandler $handler */
		foreach ($this->getHandlers() as $handler) {
			$handler->onPublish($message, $client);
		}
	}

	/**
	 * @return SplObjectStorage<IHandler, null>
	 */
	private function getHandlers(): SplObjectStorage
	{
		$this->handlers->rewind();

		return $this->handlers;
	}

}
