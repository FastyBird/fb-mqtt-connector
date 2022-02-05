<?php declare(strict_types = 1);

/**
 * PayloadHelper.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:FbMqttConnector!
 * @subpackage     Helpers
 * @since          0.1.5
 *
 * @date           21.11.21
 */

namespace FastyBird\FbMqttConnector\Helpers;

/**
 * MQTT payload helpers
 *
 * @package        FastyBird:FbMqttConnector!
 * @subpackage     Helpers
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
class PayloadHelper
{

	/**
	 * @param string $payload
	 *
	 * @return string
	 */
	public static function cleanName(string $payload): string
	{
		$cleaned = preg_replace('/[^A-Za-z0-9.,_ -]/', '', $payload);

		return is_string($cleaned) ? $cleaned : '';
	}

	/**
	 * @param string $payload
	 *
	 * @return string
	 */
	public static function cleanPayload(string $payload): string
	{
		// Remove all characters except A-Z, a-z, 0-9, dots, commas, [, ], hyphens and spaces
		// Note that the hyphen must go last not to be confused with a range (A-Z)
		// and the dot, being special, is escaped with \
		$payload = preg_replace('/[^A-Za-z0-9.:, -_°%µ³\/\"]/', '', $payload);

		if (!is_string($payload)) {
			return '';
		}

		return $payload;
	}

}
