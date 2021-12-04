<?php declare(strict_types = 1);

/**
 * ChannelProperty.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:FbMqttConnectorPlugin!
 * @subpackage     Entities
 * @since          0.1.0
 *
 * @date           05.03.20
 */

namespace FastyBird\MqttConnectorPlugin\Entities;

use Ramsey\Uuid;

/**
 * Device or channel property
 *
 * @package        FastyBird:FbMqttConnectorPlugin!
 * @subpackage     Entities
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class ChannelProperty extends Property
{

	/** @var string */
	private string $channel;

	public function __construct(
		Uuid\UuidInterface $clientId,
		string $device,
		string $channel,
		string $property,
		?string $parent = null
	) {
		parent::__construct($clientId, $device, $property, $parent);

		$this->channel = $channel;
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
	 * @return string
	 */
	public function getChannel(): string
	{
		return $this->channel;
	}

}
