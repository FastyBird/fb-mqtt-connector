<?php declare(strict_types = 1);

/**
 * IEntity.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:FbMqttConnector!
 * @subpackage     Entities
 * @since          0.1.0
 *
 * @date           05.03.20
 */

namespace FastyBird\FbMqttConnector\Entities\Messages;

use Ramsey\Uuid;

/**
 * Base data entity interface
 *
 * @package        FastyBird:FbMqttConnector!
 * @subpackage     Entities
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface IEntity
{

	/**
	 * @return Uuid\UuidInterface
	 */
	public function getConnector(): Uuid\UuidInterface;

	/**
	 * @return string
	 */
	public function getDevice(): string;

	/**
	 * @param bool $retained
	 */
	public function setRetained(bool $retained): void;

	/**
	 * @return bool
	 */
	public function isRetained(): bool;

	/**
	 * @return mixed[]
	 */
	public function toArray(): array;

}
