<?php declare(strict_types = 1);

/**
 * Entity.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:FbMqttConnector!
 * @subpackage     Entities
 * @since          0.1.0
 *
 * @date           24.02.20
 */

namespace FastyBird\FbMqttConnector\Entities\Messages;

use Nette;
use Ramsey\Uuid;

/**
 * Base data entity
 *
 * @package        FastyBird:FbMqttConnector!
 * @subpackage     Entities
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
abstract class Entity implements IEntity
{

	use Nette\SmartObject;

	/** @var Uuid\UuidInterface */
	private Uuid\UuidInterface $connector;

	/** @var string */
	private string $device;

	/** @var bool */
	private bool $retained = false;

	/**
	 * @param Uuid\UuidInterface $connector
	 * @param string $device
	 */
	public function __construct(
		Uuid\UuidInterface $connector,
		string $device
	) {
		$this->connector = $connector;
		$this->device = $device;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getConnector(): Uuid\UuidInterface
	{
		return $this->connector;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getDevice(): string
	{
		return $this->device;
	}

	/**
	 * {@inheritDoc}
	 */
	public function isRetained(): bool
	{
		return $this->retained;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setRetained(bool $retained): void
	{
		$this->retained = $retained;
	}

	/**
	 * {@inheritDoc}
	 */
	public function toArray(): array
	{
		return [
			'device'    => $this->getDevice(),
			'retained'  => $this->isRetained(),
		];
	}

}
