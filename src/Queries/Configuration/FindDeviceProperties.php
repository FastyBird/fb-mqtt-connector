<?php declare(strict_types = 1);

/**
 * FindDeviceProperties.php
 *
 * @license        More in LICENSE.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:FbMqttConnector!
 * @subpackage     Queries
 * @since          1.0.0
 *
 * @date           16.02.24
 */

namespace FastyBird\Connector\FbMqtt\Queries\Configuration;

use FastyBird\Connector\FbMqtt\Exceptions;
use FastyBird\Connector\FbMqtt\Types;
use FastyBird\Module\Devices\Documents as DevicesDocuments;
use FastyBird\Module\Devices\Queries as DevicesQueries;
use function sprintf;

/**
 * Find device properties entities query
 *
 * @template T of DevicesDocuments\Devices\Properties\Property
 * @extends  DevicesQueries\Configuration\FindDeviceProperties<T>
 *
 * @package        FastyBird:FbMqttConnector!
 * @subpackage     Queries
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
class FindDeviceProperties extends DevicesQueries\Configuration\FindDeviceProperties
{

	/**
	 * @phpstan-param Types\DevicePropertyIdentifier $identifier
	 *
	 * @throws Exceptions\InvalidArgument
	 */
	public function byIdentifier(Types\DevicePropertyIdentifier|string $identifier): void
	{
		if (!$identifier instanceof Types\DevicePropertyIdentifier) {
			throw new Exceptions\InvalidArgument(
				sprintf('Only instances of: %s are allowed', Types\DevicePropertyIdentifier::class),
			);
		}

		parent::byIdentifier($identifier->value);
	}

}
