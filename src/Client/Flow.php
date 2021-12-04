<?php declare(strict_types = 1);

/**
 * Flow.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:FbMqttConnectorPlugin!
 * @subpackage     common
 * @since          0.1.0
 *
 * @date           23.02.20
 */

namespace FastyBird\MqttConnectorPlugin\Client;

use BinSoul\Net\Mqtt;
use React\Promise;

/**
 * Decorates flows with data required for the {@see MqttClient} class.
 *
 * @package        FastyBird:FbMqttConnectorPlugin!
 * @subpackage     Client
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
class Flow implements Mqtt\Flow
{

	/** @var Mqtt\Flow */
	private Mqtt\Flow $decorated;

	/** @var Promise\Deferred */
	private Promise\Deferred $deferred;

	/** @var Mqtt\Packet|null */
	private ?Mqtt\Packet $packet = null;

	/** @var bool */
	private bool $isSilent;

	/**
	 * Constructs an instance of this class.
	 */
	public function __construct(
		Mqtt\Flow $decorated,
		Promise\Deferred $deferred,
		?Mqtt\Packet $packet = null,
		bool $isSilent = false
	) {
		$this->decorated = $decorated;
		$this->deferred = $deferred;
		$this->packet = $packet;
		$this->isSilent = $isSilent;
	}

	public function getCode(): string
	{
		return $this->decorated->getCode();
	}

	public function start(): ?Mqtt\Packet
	{
		$this->packet = $this->decorated->start();

		return $this->packet;
	}

	public function accept(Mqtt\Packet $packet): bool
	{
		return $this->decorated->accept($packet);
	}

	public function next(Mqtt\Packet $packet): ?Mqtt\Packet
	{
		$this->packet = $this->decorated->next($packet);

		return $this->packet;
	}

	public function isFinished(): bool
	{
		return $this->decorated->isFinished();
	}

	public function isSuccess(): bool
	{
		return $this->decorated->isSuccess();
	}

	public function getResult()
	{
		return $this->decorated->getResult();
	}

	public function getErrorMessage(): string
	{
		return $this->decorated->getErrorMessage();
	}

	/**
	 * Returns the associated deferred.
	 */
	public function getDeferred(): Promise\Deferred
	{
		return $this->deferred;
	}

	/**
	 * Returns the current packet.
	 */
	public function getPacket(): ?Mqtt\Packet
	{
		return $this->packet;
	}

	/**
	 * Indicates if the flow should emit events.
	 */
	public function isSilent(): bool
	{
		return $this->isSilent;
	}

}
