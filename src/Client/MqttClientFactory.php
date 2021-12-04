<?php declare(strict_types = 1);

/**
 * MqttClientFactory.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:FbMqttConnectorPlugin!
 * @subpackage     Client
 * @since          0.1.0
 *
 * @date           15.10.21
 */

namespace FastyBird\MqttConnectorPlugin\Client;

use FastyBird\MqttConnectorPlugin\Handlers;
use Nette;
use Psr\Log;
use Ramsey\Uuid;
use React\EventLoop;

/**
 * MQTT client factory
 *
 * @package         FastyBird:FbMqttConnectorPlugin!
 * @subpackage      Client
 *
 * @author          Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class MqttClientFactory
{

	use Nette\SmartObject;

	/** @var Client */
	private Client $client;

	/** @var Handlers\ClientHandler */
	private Handlers\ClientHandler $handler;

	/** @var EventLoop\LoopInterface */
	private EventLoop\LoopInterface $loop;

	/** @var Log\LoggerInterface */
	private Log\LoggerInterface $logger;

	public function __construct(
		Client $client,
		Handlers\ClientHandler $handler,
		EventLoop\LoopInterface $loop,
		?Log\LoggerInterface $logger = null
	) {
		$this->client = $client;
		$this->handler = $handler;
		$this->loop = $loop;

		$this->logger = $logger ?? new Log\NullLogger();
	}

	/**
	 * @param Uuid\UuidInterface $clientId
	 * @param string $host
	 * @param int $port
	 * @param string $username
	 * @param string $password
	 *
	 * @return void
	 */
	public function create(
		Uuid\UuidInterface $clientId,
		bool $clientState,
		string $host,
		int $port,
		string $username = '',
		string $password = ''
	): void
	{
		$client = new MqttClient(
			$clientId,
			$clientState,
			$this->handler,
			$this->loop,
			$host,
			$port,
			$username,
			$password
		);

		$this->client->registerClient($client);
	}

}
