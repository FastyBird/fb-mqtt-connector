<?php declare(strict_types = 1);

/**
 * Control.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:MqttPlugin!
 * @subpackage     Entities
 * @since          0.1.0
 *
 * @date           25.02.20
 */

namespace FastyBird\MqttPlugin\Entities;

use FastyBird\MqttPlugin\Exceptions;
use Nette\Utils;

/**
 * Device control attribute
 *
 * @package        FastyBird:MqttPlugin!
 * @subpackage     Entities
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
abstract class Control extends Entity
{

	public const DATA_TYPE_BOOLEAN = 'boolean';
	public const DATA_TYPE_NUMBER = 'number';
	public const DATA_TYPE_SELECT = 'select';
	public const DATA_TYPE_TEXT = 'text';

	public const CONFIG = 'configure';
	public const RESET = 'reset';
	public const RECONNECT = 'reconnect';
	public const FACTORY_RESET = 'factory-reset';
	public const OTA = 'ota';

	private const NOT_CONFIGURED = 'N/A';

	/** @var string */
	private $control;

	/** @var mixed[]|string|null */
	private $value = self::NOT_CONFIGURED;

	/** @var mixed[]|string|null */
	private $schema = self::NOT_CONFIGURED;

	public function __construct(
		string $device,
		string $control,
		?string $parent = null
	) {
		if (!in_array($control, $this->getAllowedControls(), true)) {
			throw new Exceptions\InvalidArgumentException(sprintf('Provided control "%s" is not in allowed range', $control));
		}

		parent::__construct($device, $parent);

		$this->control = $control;
	}

	/**
	 * @return string
	 */
	public function getControl(): string
	{
		return $this->control;
	}

	/**
	 * @param string $value
	 *
	 * @return void
	 */
	public function setValue(?string $value): void
	{
		$this->value = $value;

		if ($this->control === self::CONFIG && $value !== null) {
			try {
				$this->value = Utils\Json::decode($value, Utils\Json::FORCE_ARRAY);

			} catch (Utils\JsonException $ex) {
				throw new Exceptions\ParseMessageException('Control config payload is not valid JSON value');
			}
		}
	}

	/**
	 * @return mixed[]|string|null
	 */
	public function getValue()
	{
		return $this->value;
	}

	/**
	 * @param mixed[]|null $schema
	 */
	public function setSchema(?array $schema): void
	{
		if (!$this->isConfiguration()) {
			throw new Exceptions\InvalidStateException(sprintf('Schema could be set only for "%s" control type', self::CONFIG));
		}

		$this->schema = $schema;
	}

	/**
	 * @return mixed[]|string|null
	 */
	public function getSchema()
	{
		if (!$this->isConfiguration()) {
			throw new Exceptions\InvalidStateException(sprintf('Schema could be get only for "%s" control type', self::CONFIG));
		}

		return $this->schema;
	}

	/**
	 * @return bool
	 */
	public function isConfiguration(): bool
	{
		return $this->control === self::CONFIG;
	}

	/**
	 * @return mixed[]
	 */
	public function toArray(): array
	{
		$return = array_merge([
			'control' => $this->getControl(),
		], parent::toArray());

		if ($this->getValue() !== self::NOT_CONFIGURED) {
			$return['value'] = $this->getValue();
		}

		if ($this->isConfiguration() && $this->getSchema() !== self::NOT_CONFIGURED) {
			$return['schema'] = $this->getSchema();
		}

		return $return;
	}

	/**
	 * @return string[]
	 */
	protected function getAllowedControls(): array
	{
		return [];
	}

}
