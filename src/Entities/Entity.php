<?php declare(strict_types = 1);

/**
 * Entity.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:MqttConnectorPlugin!
 * @subpackage     Entities
 * @since          0.1.0
 *
 * @date           24.02.20
 */

namespace FastyBird\MqttConnectorPlugin\Entities;

use Nette;
use Ramsey\Uuid;

/**
 * Base data entity
 *
 * @package        FastyBird:MqttConnectorPlugin!
 * @subpackage     Entities
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
abstract class Entity implements IEntity
{

	use Nette\SmartObject;

	/** @var Uuid\UuidInterface */
	private Uuid\UuidInterface $clientId;

	/** @var string */
	private string $device;

	/** @var string|null */
	private ?string $parent;

	/** @var bool */
	private bool $retained = false;

	public function __construct(
		Uuid\UuidInterface $clientId,
		string $device,
		?string $parent = null
	) {
		$this->clientId = $clientId;
		$this->device = $device;
		$this->parent = $parent;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getClientId(): Uuid\UuidInterface
	{
		return $this->clientId;
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

	/**
	 * {@inheritDoc}
	 */
	public function toArray(): array
	{
		return [
			'client_id' => $this->getClientId()->toString(),
			'device'    => $this->getDevice(),
			'parent'    => $this->getParent(),
			'retained'  => $this->isRetained(),
		];
	}

}
