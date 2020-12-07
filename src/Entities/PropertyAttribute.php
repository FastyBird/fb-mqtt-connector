<?php declare(strict_types = 1);

/**
 * PropertyAttribute.php
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

use FastyBird\MqttPlugin;
use FastyBird\MqttPlugin\Exceptions;
use Nette;

/**
 * Device or channel property attribute
 *
 * @package        FastyBird:MqttPlugin!
 * @subpackage     Entities
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class PropertyAttribute
{

	use Nette\SmartObject;

	public const NAME = 'name';
	public const TYPE = 'type';
	public const SETTABLE = 'settable';
	public const QUERYABLE = 'queryable';
	public const DATATYPE = 'datatype';
	public const FORMAT = 'format';
	public const UNIT = 'unit';

	public const ALLOWED_ATTRIBUTES = [
		self::NAME,
		self::TYPE,
		self::SETTABLE,
		self::QUERYABLE,
		self::DATATYPE,
		self::FORMAT,
		self::UNIT,
	];

	public const DATATYPE_ALLOWED_PAYLOADS = [
		'string',
		'integer',
		'float',
		'boolean',
		'enum',
		'color',
	];

	public const FORMAT_ALLOWED_PAYLOADS = [
		'rgb',
		'hsv',
	];

	/** @var string */
	private $attribute;

	/** @var string|null */
	private $value = null;

	public function __construct(
		string $attribute,
		?string $value
	) {
		if (!in_array($attribute, self::ALLOWED_ATTRIBUTES, true)) {
			throw new Exceptions\InvalidArgumentException(sprintf('Provided property parameter "%s" is not in allowed range', $attribute));
		}

		$this->attribute = $attribute;
		$this->value = $value;
	}

	/**
	 * @return mixed[]
	 */
	public function toArray(): array
	{
		return [
			'attribute' => $this->getAttribute(),
			'value'     => $this->getValue(),
		];
	}

	/**
	 * @return string
	 */
	public function getAttribute(): string
	{
		return $this->attribute;
	}

	/**
	 * @return string|bool|null
	 */
	public function getValue()
	{
		if ($this->value === null) {
			return null;
		}

		if (
			$this->attribute === self::SETTABLE
			|| $this->attribute === self::QUERYABLE
		) {
			return $this->value === MqttPlugin\Constants::PAYLOAD_BOOL_TRUE_VALUE;
		}

		return $this->value;
	}

}
