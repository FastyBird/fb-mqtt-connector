<?php declare(strict_types = 1);

/**
 * ExtensionAttribute.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:FbMqttConnector!
 * @subpackage     Entities
 * @since          0.25.0
 *
 * @date           05.07.22
 */

namespace FastyBird\FbMqttConnector\Entities\Messages;

use FastyBird\FbMqttConnector\Exceptions;
use FastyBird\FbMqttConnector\Types;
use Ramsey\Uuid;

/**
 * Device extension attribute
 *
 * @package        FastyBird:FbMqttConnector!
 * @subpackage     Entities
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class ExtensionAttribute extends Entity
{

	public const MAC_ADDRESS = 'mac-address';
	public const MANUFACTURER = 'manufacturer';
	public const MODEL = 'model';
	public const VERSION = 'version';
	public const NAME = 'name';

	public const ALLOWED_PARAMETERS = [
		self::MAC_ADDRESS,
		self::MANUFACTURER,
		self::MODEL,
		self::VERSION,
		self::NAME,
	];

	/** @var Types\ExtensionType */
	private Types\ExtensionType $extension;

	/** @var string */
	private string $parameter;

	/** @var string */
	private string $value;

	/**
	 * @param Uuid\UuidInterface $connector
	 * @param string $device
	 * @param Types\ExtensionType $extension
	 * @param string $parameter
	 * @param string $value
	 */
	public function __construct(
		Uuid\UuidInterface $connector,
		string $device,
		Types\ExtensionType $extension,
		string $parameter,
		string $value
	) {
		if (!in_array($parameter, self::ALLOWED_PARAMETERS, true)) {
			throw new Exceptions\InvalidArgument(sprintf('Provided extension attribute "%s" is not in allowed range', $parameter));
		}

		parent::__construct($connector, $device);

		$this->extension = $extension;
		$this->parameter = $parameter;
		$this->value = $value;
	}

	/**
	 * @return Types\ExtensionType
	 */
	public function getExtension(): Types\ExtensionType
	{
		return $this->extension;
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
