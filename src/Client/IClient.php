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

use FastyBird\FbMqttConnector;
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
