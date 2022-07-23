<?php declare(strict_types = 1);

/**
 * PropertyAttribute.php
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

use FastyBird\FbMqttConnector;
use FastyBird\FbMqttConnector\Exceptions;
use FastyBird\FbMqttConnector\Helpers;
use FastyBird\Metadata\Types as MetadataTypes;
use Nette;
use Nette\Utils;

/**
 * Device or channel property attribute
 *
 * @package        FastyBird:FbMqttConnector!
 * @subpackage     Entities
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class PropertyAttributeEntity
{

	use Nette\SmartObject;

	public const NAME = 'name';
	public const SETTABLE = 'settable';
	public const QUERYABLE = 'queryable';
	public const DATA_TYPE = 'data-type';
	public const FORMAT = 'format';
	public const UNIT = 'unit';

	public const ALLOWED_ATTRIBUTES = [
		self::NAME,
		self::SETTABLE,
		self::QUERYABLE,
		self::DATA_TYPE,
		self::FORMAT,
		self::UNIT,
	];

	public const FORMAT_ALLOWED_PAYLOADS = [
		'rgb',
		'hsv',
	];

	/** @var string */
	private string $attribute;

	/** @var string|string[]|float[]|null[]|bool|MetadataTypes\DataTypeType|null */
	private MetadataTypes\DataTypeType|string|array|bool|null $value = null;

	/**
	 * @param string $attribute
	 * @param string $value
	 */
	public function __construct(
		string $attribute,
		string $value
	) {
		if (!in_array($attribute, self::ALLOWED_ATTRIBUTES, true)) {
			throw new Exceptions\InvalidArgumentException(sprintf('Provided property parameter "%s" is not in allowed range', $attribute));
		}

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
	 * @return string|string[]|float[]|null[]|bool|MetadataTypes\DataTypeType|null
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
			return $this->value === FbMqttConnector\Constants::PAYLOAD_BOOL_TRUE_VALUE;
		}

		return $this->value;
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
	 * @param string $value
	 *
	 * @return void
	 */
	private function parseValue(string $value): void
	{
		if (
			$this->getAttribute() === self::SETTABLE
			|| $this->getAttribute() === self::QUERYABLE
		) {
			$this->value = $value === FbMqttConnector\Constants::PAYLOAD_BOOL_TRUE_VALUE ? FbMqttConnector\Constants::PAYLOAD_BOOL_TRUE_VALUE : FbMqttConnector\Constants::PAYLOAD_BOOL_FALSE_VALUE;

		} elseif ($this->getAttribute() === self::NAME) {
			$this->value = Helpers\PayloadHelper::cleanName($value);

		} elseif ($this->getAttribute() === self::DATA_TYPE) {
			if (!MetadataTypes\DataTypeType::isValidValue($value)) {
				throw new Exceptions\ParseMessageException('Provided payload is not valid');
			}

			$this->value = MetadataTypes\DataTypeType::get($value);

		} elseif ($this->getAttribute() === self::FORMAT) {
			if (Utils\Strings::contains($value, ':')) {
				[$start, $end] = explode(':', $value) + [null, null];

				$start = $start === '' ? null : $start;
				$end = $end === '' ? null : $end;

				if ($start !== null && is_numeric($start) === false) {
					throw new Exceptions\ParseMessageException('Provided payload is not valid');
				}

				if ($end !== null && is_numeric($end) === false) {
					throw new Exceptions\ParseMessageException('Provided payload is not valid');
				}

				if ($start !== null) {
					$start = (float) $start;
				}

				if ($end !== null) {
					$end = (float) $end;
				}

				if ($start !== null && $end !== null && $start > $end) {
					throw new Exceptions\ParseMessageException('Provided payload is not valid');
				}

				$this->value = [$start, $end];

			} elseif (Utils\Strings::contains($value, ',')) {
				$value = array_filter(
					array_map('trim', explode(',', strtolower($value))),
					function ($item): bool {
						return $item !== '';
					}
				);

				$value = array_values($value);

				$this->value = array_unique($value);

			} elseif ($value === FbMqttConnector\Constants::VALUE_NOT_SET || $value === '') {
				$this->value = null;

			} elseif (!in_array($value, self::FORMAT_ALLOWED_PAYLOADS, true)) {
				throw new Exceptions\ParseMessageException('Provided payload is not valid');
			}
		} else {
			$this->value = $value === FbMqttConnector\Constants::VALUE_NOT_SET || $value === '' ? null : $value;
		}
	}

}
