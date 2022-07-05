<?php declare(strict_types = 1);

/**
 * IFbMqttConnector.php
 *
 * @license        More in LICENSE.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:FbMqttConnector!
 * @subpackage     Entities
 * @since          0.4.0
 *
 * @date           05.02.22
 */

namespace FastyBird\FbMqttConnector\Entities;

use FastyBird\DevicesModule\Entities as DevicesModuleEntities;
use FastyBird\FbMqttConnector\Types;

/**
 * FastyBird MQTT connector entity interface
 *
 * @package        FastyBird:FbMqttConnector!
 * @subpackage     Entities
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface IFbMqttConnector extends DevicesModuleEntities\Connectors\IConnector
{

	/**
	 * @return string
	 */
	public function getServer(): string;

	/**
	 * @return int
	 */
	public function getPort(): int;

	/**
	 * @return int
	 */
	public function getSecuredPort(): int;

	/**
	 * @return string|null
	 */
	public function getUsername(): ?string;

	/**
	 * @return string|null
	 */
	public function getPassword(): ?string;

	/**
	 * @return Types\ProtocolVersionType
	 */
	public function getVersion(): Types\ProtocolVersionType;

}
