<?php declare(strict_types = 1);

/**
 * ChannelAttribute.php
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
 * Channel attribute
 *
 * @package        FastyBird:FbMqttConnector!
 * @subpackage     Entities
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class ChannelAttribute extends Attribute
{

	public const ALLOWED_ATTRIBUTES = [
		self::NAME,
		self::PROPERTIES,
		self::CONTROL,
	];

	/** @var string */
	private string $channel;

	/**
	 * @param string $device
	 * @param string $channel
	 * @param string $attribute
	 * @param string $value
	 * @param string|null $parent
	 */
	public function __construct(
		string $device,
		string $channel,
		string $attribute,
		string $value,
		?string $parent = null
	) {
		parent::__construct($device, $attribute, $value, $parent);

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
	protected function getAllowedAttributes(): array
	{
		return self::ALLOWED_ATTRIBUTES;
	}

}
