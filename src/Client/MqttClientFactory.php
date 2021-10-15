<?php declare(strict_types = 1);

/**
 * MqttClientFactory.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:MqttConnectorPlugin!
 * @subpackage     Client
 * @since          0.1.0
 *
 * @date           15.10.21
 */

namespace FastyBird\MqttConnectorPlugin\Client;

/**
 * MQTT client factory
 *
 * @package         FastyBird:MqttConnectorPlugin!
 * @subpackage      Client
 *
 * @author          Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface MqttClientFactory
{

	/**
	 * @param ConnectionSettings $connectionSettings
	 *
	 * @return MqttClient
	 */
	public function create(ConnectionSettings $connectionSettings): MqttClient;

}
