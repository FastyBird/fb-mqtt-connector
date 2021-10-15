<?php declare(strict_types = 1);

/**
 * IHandler.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:MqttConnectorPlugin!
 * @subpackage     Handlers
 * @since          0.1.0
 *
 * @date           15.10.21
 */

namespace FastyBird\MqttConnectorPlugin\Handlers;

use BinSoul\Net\Mqtt;
use FastyBird\MqttConnectorPlugin\Client;
use Throwable;

/**
 * MQTT client handler interface
 *
 * @package         FastyBird:MqttConnectorPlugin!
 * @subpackage      Handlers
 *
 * @author          Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface IHandler
{

	/**
	 * @param Mqtt\Connection $connection
	 * @param Client\MqttClient $client
	 *
	 * @return void
	 */
	public function onOpen(Mqtt\Connection $connection, Client\MqttClient $client): void;

	/**
	 * @param Mqtt\Connection $connection
	 * @param Client\MqttClient $client
	 *
	 * @return void
	 */
	public function onClose(Mqtt\Connection $connection, Client\MqttClient $client): void;

	/**
	 * @param Mqtt\Connection $connection
	 * @param Client\MqttClient $client
	 *
	 * @return void
	 */
	public function onConnect(Mqtt\Connection $connection, Client\MqttClient $client): void;

	/**
	 * @param Mqtt\Connection $connection
	 * @param Client\MqttClient $client
	 *
	 * @return void
	 */
	public function onDisconnect(Mqtt\Connection $connection, Client\MqttClient $client): void;

	/**
	 * @param Throwable $ex
	 * @param Client\MqttClient $client
	 *
	 * @return void
	 */
	public function onWarning(Throwable $ex, Client\MqttClient $client): void;

	/**
	 * @param Throwable $ex
	 * @param Client\MqttClient $client
	 *
	 * @return void
	 */
	public function onError(Throwable $ex, Client\MqttClient $client): void;

	/**
	 * @param Mqtt\Message $message
	 * @param Client\MqttClient $client
	 *
	 * @return void
	 */
	public function onMessage(Mqtt\Message $message, Client\MqttClient $client): void;

	/**
	 * @param Mqtt\Subscription $subscription
	 * @param Client\MqttClient $client
	 *
	 * @return void
	 */
	public function onSubscribe(Mqtt\Subscription $subscription, Client\MqttClient $client): void;

	/**
	 * @param Mqtt\Subscription[] $subscriptions
	 * @param Client\MqttClient $client
	 *
	 * @return void
	 */
	public function onUnsubscribe(array $subscriptions, Client\MqttClient $client): void;

	/**
	 * @param Mqtt\Message $message
	 * @param Client\MqttClient $client
	 *
	 * @return void
	 */
	public function onPublish(Mqtt\Message $message, Client\MqttClient $client): void;

}
