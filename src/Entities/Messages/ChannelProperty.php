<?php declare(strict_types = 1);

/**
 * ChannelProperty.php
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

/**
 * Device or channel property
 *
 * @package        FastyBird:FbMqttConnector!
 * @subpackage     Entities
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class ChannelProperty extends Property
{

	/** @var string */
	private string $channel;

	public function __construct(
		string $device,
		string $channel,
		string $property
	) {
		parent::__construct($device, $property);

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
