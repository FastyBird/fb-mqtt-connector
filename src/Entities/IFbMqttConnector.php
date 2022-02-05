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
	 * @param string|null $server
	 *
	 * @return void
	 */
	public function setServer(?string $server): void;

	/**
	 * @return string
	 */
	public function getServer(): string;

	/**
	 * @param int|null $port
	 *
	 * @return void
	 */
	public function setPort(?int $port): void;

	/**
	 * @return int
	 */
	public function getPort(): int;

	/**
	 * @param int|null $port
	 *
	 * @return void
	 */
	public function setSecuredPort(?int $port): void;

	/**
	 * @return int
	 */
	public function getSecuredPort(): int;

	/**
	 * @param string|null $username
	 *
	 * @return void
	 */
	public function setUsername(?string $username): void;

	/**
	 * @return string|null
	 */
	public function getUsername(): ?string;

	/**
	 * @param string|null $password
	 *
	 * @return void
	 */
	public function setPassword(?string $password): void;

	/**
	 * @return string|null
	 */
	public function getPassword(): ?string;

	/**
	 * @return Types\ProtocolVersionType
	 */
	public function getProtocol(): Types\ProtocolVersionType;

	/**
	 * @param Types\ProtocolVersionType|null $protocol
	 *
	 * @return void
	 */
	public function setProtocol(?Types\ProtocolVersionType $protocol): void;

}
