<?php declare(strict_types = 1);

/**
 * ParseMessageException.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:MqttConnectorPlugin!
 * @subpackage     Exceptions
 * @since          0.1.0
 *
 * @date           25.02.20
 */

namespace FastyBird\MqttConnectorPlugin\Exceptions;

use RuntimeException;

class ParseMessageException extends RuntimeException implements IException
{

}
