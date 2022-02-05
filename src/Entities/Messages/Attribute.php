<?php declare(strict_types = 1);

/**
 * Attribute.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:FbMqttConnector!
 * @subpackage     Entities
 * @since          0.1.0
 *
 * @date           24.02.20
 */

namespace FastyBird\FbMqttConnector\Entities\Messages;

use FastyBird\FbMqttConnector\Exceptions;
use FastyBird\FbMqttConnector\Helpers;

/**
 * Device, channel or property attribute
 *
 * @package        FastyBird:FbMqttConnector!
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
	private string $attribute;

	/** @var string|string[] */
	private $value;

	/**
	 * @param string $device
	 * @param string $attribute
	 * @param string $value
	 * @param string|null $parent
	 */
	public function __construct(
		string $device,
		string $attribute,
		string $value,
		?string $parent = null
	) {
		if (!in_array($attribute, $this->getAllowedAttributes(), true)) {
			throw new Exceptions\InvalidArgumentException(sprintf('Provided attribute "%s" is not in allowed range', $attribute));
		}

		parent::__construct($device, $parent);

		$this->attribute = $attribute;

		$this->parseValue($value);
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

	/**
	 * @param string $value
	 *
	 * @return void
	 */
	private function parseValue(string $value): void
	{
		if ($this->getAttribute() === self::NAME) {
			$this->value = Helpers\PayloadHelper::cleanName($value);

		} else {
			$this->value = Helpers\PayloadHelper::cleanPayload($value);

			if (
				$this->getAttribute() === self::PROPERTIES
				|| $this->getAttribute() === self::CHANNELS
				|| $this->getAttribute() === self::EXTENSIONS
				|| $this->getAttribute() === self::CONTROL
			) {
				$items = array_filter(
					array_map('trim', explode(',', strtolower($value))),
					function ($item): bool {
						return $item !== '';
					}
				);

				$items = array_values($items);

				$this->value = array_unique($items);
			}
		}
	}

}
