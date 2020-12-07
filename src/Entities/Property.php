<?php declare(strict_types = 1);

/**
 * Property.php
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

use SplObjectStorage;

/**
 * Device or channel property
 *
 * @package        FastyBird:MqttPlugin!
 * @subpackage     Entities
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
abstract class Property extends Entity
{

	private const NOT_CONFIGURED = 'N/A';

	/** @var string */
	private $property;

	/** @var string */
	private $value = self::NOT_CONFIGURED;

	/** @var SplObjectStorage */
	private $attributes;

	public function __construct(
		string $device,
		string $property,
		?string $parent = null
	) {
		parent::__construct($device, $parent);

		$this->property = $property;

		$this->attributes = new SplObjectStorage();
	}

	/**
	 * @param PropertyAttribute $attribute
	 *
	 * @return void
	 */
	public function addAttribute(PropertyAttribute $attribute): void
	{
		if (!$this->attributes->contains($attribute)) {
			$this->attributes->attach($attribute);
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function toArray(): array
	{
		$return = array_merge([
			'property' => $this->getProperty(),
		], parent::toArray());

		foreach ($this->getAttributes() as $attribute) {
			$return[$attribute->getAttribute()] = $attribute->getValue();
		}

		if ($this->getValue() !== self::NOT_CONFIGURED) {
			$return['value'] = $this->getValue();
		}

		return $return;
	}

	/**
	 * @return string
	 */
	public function getProperty(): string
	{
		return $this->property;
	}

	/**
	 * @return PropertyAttribute[]
	 */
	public function getAttributes(): array
	{
		$data = [];

		/** @var PropertyAttribute $item */
		foreach ($this->attributes as $item) {
			$data[] = $item;
		}

		return $data;
	}

	/**
	 * @return string
	 */
	public function getValue(): string
	{
		return $this->value;
	}

	/**
	 * @param string $value
	 *
	 * @return void
	 */
	public function setValue(string $value): void
	{
		$this->value = $value;
	}

}
