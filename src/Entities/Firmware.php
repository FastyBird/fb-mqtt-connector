<?php declare(strict_types = 1);

/**
 * Firmware.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:MqttConnectorPlugin!
 * @subpackage     Entities
 * @since          0.1.0
 *
 * @date           24.02.20
 */

namespace FastyBird\MqttConnectorPlugin\Entities;

use FastyBird\MqttConnectorPlugin\Exceptions;

/**
 * Device firmware
 *
 * @package        FastyBird:MqttConnectorPlugin!
 * @subpackage     Entities
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class Firmware extends Entity
{

	public const MANUFACTURER = 'manufacturer';
	public const NAME = 'name';
	public const VERSION = 'version';

	public const ALLOWED_PARAMETERS = [
		self::MANUFACTURER,
		self::NAME,
		self::VERSION,
	];

	/** @var string */
	private string $parameter;

	/** @var string */
	private string $value;

	public function __construct(
		string $device,
		string $parameter,
		string $value,
		?string $parent = null
	) {
		if (!in_array($parameter, self::ALLOWED_PARAMETERS, true)) {
			throw new Exceptions\InvalidArgumentException(sprintf('Provided firmware attribute "%s" is not in allowed range', $parameter));
		}

		parent::__construct($device, $parent);

		$this->parameter = $parameter;
		$this->value = $value;
	}

	/**
	 * {@inheritDoc}
	 */
	public function toArray(): array
	{
		return array_merge([
			$this->getParameter() => $this->getValue(),
		], parent::toArray());
	}

	/**
	 * @return string
	 */
	public function getParameter(): string
	{
		return $this->parameter;
	}

	/**
	 * @return string
	 */
	public function getValue(): string
	{
		return $this->value;
	}

}
