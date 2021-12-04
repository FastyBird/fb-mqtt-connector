<?php declare(strict_types = 1);

/**
 * LogicException.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:FbMqttConnectorPlugin!
 * @subpackage     Exceptions
 * @since          0.1.0
 *
 * @date           15.10.21
 */

namespace FastyBird\MqttConnectorPlugin\Exceptions;

use LogicException as PHPLogicException;

class LogicException extends PHPLogicException implements IException
{

}
