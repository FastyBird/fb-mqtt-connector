<?php declare(strict_types = 1);

/**
 * InvalidState.php
 *
 * @license        More in LICENSE.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:FbMqttConnector!
 * @subpackage     Exceptions
 * @since          1.0.0
 *
 * @date           09.02.23
 */

namespace FastyBird\Connector\FbMqtt\Exceptions;

use RuntimeException;

class InvalidState extends RuntimeException implements Exception
{

}
