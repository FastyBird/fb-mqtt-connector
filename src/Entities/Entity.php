<?php declare(strict_types = 1);

/**
 * Entity.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:MqttPlugin!
 * @subpackage     Entities
 * @since          0.1.0
 *
 * @date           24.02.20
 */

namespace FastyBird\MqttPlugin\Entities;

use Nette;

/**
 * Base data entity
 *
 * @package        FastyBird:MqttPlugin!
 * @subpackage     Entities
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
abstract class Entity implements IEntity
{

	use Nette\SmartObject;

	/** @var string */
	private $device;

	/** @var string|null */
	private $parent;

	/** @var bool */
	private $retained = false;

	public function __construct(
		string $device,
		?string $parent = null
	) {
		$this->device = $device;
		$this->parent = $parent;
	}

	/**
	 * {@inheritDoc}
	 */
	public function toArray(): array
	{
		return [
			'device'   => $this->getDevice(),
			'parent'   => $this->getParent(),
			'retained' => $this->isRetained(),
		];
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
	public function getParent(): ?string
	{
		return $this->parent;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setParent(?string $parent): void
	{
		$this->parent = $parent;
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

}
