<?php declare(strict_types = 1);

/**
 * Control.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:MqttConnectorPlugin!
 * @subpackage     Entities
 * @since          0.1.0
 *
 * @date           05.03.20
 */

namespace FastyBird\MqttConnectorPlugin\Entities;

use Ramsey\Uuid;

/**
 * Channel control attribute
 *
 * @package        FastyBird:MqttConnectorPlugin!
 * @subpackage     Entities
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
class ChannelControl extends Control
{

	public const ALLOWED_CONTROLS = [
		self::CONFIG,
	];

	/** @var string */
	private string $channel;

	public function __construct(
		Uuid\UuidInterface $clientId,
		string $device,
		string $channel,
		string $control,
		?string $parent = null
	) {
		parent::__construct($clientId, $device, $control, $parent);

		$this->channel = $channel;
	}

	/**
	 * @return string
	 */
	public function getChannel(): string
	{
		return $this->channel;
	}

	/**
	 * {@inheritDoc}
	 */
	public function toArray(): array
	{
		return array_merge([
			'channel' => $this->getChannel(),
		], parent::toArray());
	}

	/**
	 * {@inheritDoc}
	 */
	protected function getAllowedControls(): array
	{
		return self::ALLOWED_CONTROLS;
	}

}
