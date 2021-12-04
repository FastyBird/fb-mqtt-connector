<?php declare(strict_types = 1);

/**
 * Hardware.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:FbMqttConnectorPlugin!
 * @subpackage     Entities
 * @since          0.1.0
 *
 * @date           24.02.20
 */

namespace FastyBird\MqttConnectorPlugin\Entities;

use FastyBird\MqttConnectorPlugin\Exceptions;
use Ramsey\Uuid;

/**
 * Device hardware
 *
 * @package        FastyBird:FbMqttConnectorPlugin!
 * @subpackage     Entities
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class Hardware extends Entity
{

	public const MAC_ADDRESS = 'mac-address';
	public const MANUFACTURER = 'manufacturer';
	public const MODEL = 'model';
	public const VERSION = 'version';
	public const SN = 'serial-number';

	public const ALLOWED_PARAMETERS = [
		self::MAC_ADDRESS,
		self::MANUFACTURER,
		self::MODEL,
		self::VERSION,
		self::SN,
	];

	/** @var string */
	private string $parameter;

	/** @var string */
	private string $value;

	public function __construct(
		Uuid\UuidInterface $clientId,
		string $device,
		string $parameter,
		string $value,
		?string $parent = null
	) {
		if (!in_array($parameter, self::ALLOWED_PARAMETERS, true)) {
			throw new Exceptions\InvalidArgumentException(sprintf('Provided hardware attribute "%s" is not in allowed range', $parameter));
		}

		parent::__construct($clientId, $device, $parent);

		$this->parameter = $parameter;
		$this->value = $value;
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

	/**
	 * {@inheritDoc}
	 */
	public function toArray(): array
	{
		return array_merge([
			$this->getParameter() => $this->getValue(),
		], parent::toArray());
	}

}
