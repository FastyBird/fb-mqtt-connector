<?php declare(strict_types = 1);

/**
 * Connection.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:MqttConnectorPlugin!
 * @subpackage     Client
 * @since          0.1.0
 *
 * @date           15.10.21
 */

namespace FastyBird\MqttConnectorPlugin\Client;

use BinSoul\Net\Mqtt;
use Nette;

/**
 * MQTT clients connection settings
 *
 * @package        FastyBird:MqttConnectorPlugin!
 * @subpackage     Client
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class ConnectionSettings
{

	use Nette\SmartObject;

	/** @var string */
	private string $host;

	/** @var int */
	private int $port;

	/** @var string */
	private string $username;

	/** @var string */
	private string $password;

	/** @var ?Mqtt\Message */
	private ?Mqtt\Message $will;

	/** @var string */
	private string $clientId;

	public function __construct(
		string $host,
		int $port,
		string $clientId,
		string $username = '',
		string $password = '',
		?Mqtt\Message $will = null
	) {
		$this->host = $host;
		$this->port = $port;
		$this->clientId = $clientId;
		$this->username = $username;
		$this->password = $password;
		$this->will = $will;
	}

	/**
	 * @return string
	 */
	public function getHost(): string
	{
		return $this->host;
	}

	/**
	 * @return int
	 */
	public function getPort(): int
	{
		return $this->port;
	}

	/**
	 * @return string
	 */
	public function getUsername(): string
	{
		return $this->username;
	}

	/**
	 * @return string
	 */
	public function getPassword(): string
	{
		return $this->password;
	}

	/**
	 * @return Mqtt\Message|null
	 */
	public function getWill(): ?Mqtt\Message
	{
		return $this->will;
	}

	/**
	 * @return string
	 */
	public function getClientId(): string
	{
		return $this->clientId;
	}

}
