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
use Ramsey\Uuid;

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
	public const CONTROLS = 'controls';

	/** @var string */
	private string $attribute;

	/** @var string|string[] */
	private string|array $value;

	/**
	 * @param Uuid\UuidInterface $connector
	 * @param string $device
	 * @param string $attribute
	 * @param string $value
	 */
	public function __construct(
		Uuid\UuidInterface $connector,
		string $device,
		string $attribute,
		string $value
	) {
		if (!in_array($attribute, $this->getAllowedAttributes(), true)) {
			throw new Exceptions\InvalidArgument(sprintf('Provided attribute "%s" is not in allowed range', $attribute));
		}

		parent::__construct($connector, $device);

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
	public function getValue(): array|string
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
			$this->value = Helpers\Payload::cleanName($value);

		} else {
			$this->value = Helpers\Payload::cleanPayload($value);

			if (
				$this->getAttribute() === self::PROPERTIES
				|| $this->getAttribute() === self::CHANNELS
				|| $this->getAttribute() === self::EXTENSIONS
				|| $this->getAttribute() === self::CONTROLS
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
