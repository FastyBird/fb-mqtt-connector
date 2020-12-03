<?php declare(strict_types = 1);

/**
 * Attribute.php
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

use FastyBird\MqttPlugin\Exceptions;

/**
 * Device, channel or property attribute
 *
 * @package        FastyBird:MqttPlugin!
 * @subpackage     Entities
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
abstract class Attribute extends Entity
{

	public const NAME = 'name';
	public const PROPERTIES = 'properties';
	public const STATE = 'state';
	public const CHANNELS = 'channels';
	public const EXTENSIONS = 'extensions';
	public const CONTROL = 'control';

	/** @var string */
	private $attribute;

	/** @var string|string[] */
	private $value;

	/**
	 * @param string $device
	 * @param string $attribute
	 * @param string|string[] $value
	 * @param string|null $parent
	 */
	public function __construct(
		string $device,
		string $attribute,
		$value,
		?string $parent = null
	) {
		if (!in_array($attribute, $this->getAllowedAttributes(), true)) {
			throw new Exceptions\InvalidArgumentException(sprintf('Provided attribute "%s" is not in allowed range', $attribute));
		}

		parent::__construct($device, $parent);

		$this->attribute = $attribute;
		$this->value = $value;
	}

	/**
	 * @return string
	 */
	public function getAttribute(): string
	{
		return $this->attribute;
	}

	/**
	 * @return string|string[]
	 */
	public function getValue()
	{
		return $this->value;
	}

	/**
	 * {@inheritDoc}
	 */
	public function toArray(): array
	{
		return array_merge([
			$this->getAttribute() => $this->getValue(),
		], parent::toArray());
	}

	/**
	 * @return string[]
	 */
	protected function getAllowedAttributes(): array
	{
		return [];
	}

}
