<?php declare(strict_types = 1);

/**
 * ChannelPropertyEntity.php
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
 * Device or channel property
 *
 * @package        FastyBird:FbMqttConnector!
 * @subpackage     Entities
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class ChannelPropertyEntity extends PropertyEntity
{

	/** @var string */
	private string $channel;

	/**
	 * @param Uuid\UuidInterface $connector
	 * @param string $device
	 * @param string $channel
	 * @param string $property
	 */
	public function __construct(
		Uuid\UuidInterface $connector,
		string $device,
		string $channel,
		string $property
	) {
		parent::__construct($connector, $device, $property);

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
