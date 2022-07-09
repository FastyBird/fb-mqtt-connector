<?php declare(strict_types = 1);

/**
 * IClient.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:FbMqttConnector!
 * @subpackage     Client
 * @since          0.1.0
 *
 * @date           23.02.20
 */

namespace FastyBird\FbMqttConnector\Client;

use BinSoul\Net\Mqtt;
use FastyBird\FbMqttConnector;
use FastyBird\FbMqttConnector\Types;
use FastyBird\Metadata\Entities as MetadataEntities;
use React\Promise;

/**
 * FastyBird MQTT client interface
 *
 * @package        FastyBird:FbMqttConnector!
 * @subpackage     Client
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface IClient
{

	/**
	 * @return Types\ProtocolVersionType
	 */
	public function getVersion(): Types\ProtocolVersionType;

	/**
	 * @param MetadataEntities\Actions\IActionDeviceControlEntity $action
	 *
	 * @return void
	 */
	public function writeDeviceControl(MetadataEntities\Actions\IActionDeviceControlEntity $action): void;

	/**
	 * @param MetadataEntities\Actions\IActionChannelControlEntity $action
	 *
	 * @return void
	 */
	public function writeChannelControl(MetadataEntities\Actions\IActionChannelControlEntity $action): void;

	/**
	 * @return bool
	 */
	public function isConnected(): bool;

	/**
	 * Connects to a broker
	 *
	 * @param int $timeout
	 *
	 * @return Promise\ExtendedPromiseInterface
	 */
	public function connect(int $timeout = 5): Promise\ExtendedPromiseInterface;

	/**
	 * Disconnects from a broker
	 *
	 * @param int $timeout
	 *
	 * @return Promise\ExtendedPromiseInterface
	 */
	public function disconnect(int $timeout = 5): Promise\ExtendedPromiseInterface;

	/**
	 * Subscribes to a topic filter
	 *
	 * @param Mqtt\Subscription $subscription
	 *
	 * @return Promise\ExtendedPromiseInterface
	 */
	public function subscribe(Mqtt\Subscription $subscription): Promise\ExtendedPromiseInterface;

	/**
	 * Unsubscribes from a topic filter
	 *
	 * @param Mqtt\Subscription $subscription
	 *
	 * @return Promise\ExtendedPromiseInterface
	 */
	public function unsubscribe(Mqtt\Subscription $subscription): Promise\ExtendedPromiseInterface;

	/**
	 * @param string $topic
	 * @param string|null $payload
	 * @param int $qos
	 * @param bool $retain
	 *
	 * @return Promise\ExtendedPromiseInterface
	 */
	public function publish(
		string $topic,
		?string $payload,
		int $qos = FbMqttConnector\Constants::MQTT_API_QOS_0,
		bool $retain = false
	): Promise\ExtendedPromiseInterface;

}
