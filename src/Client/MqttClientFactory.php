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
use React\EventLoop;
use Throwable;

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
	 * @param string $host
	 * @param int $port
	 * @param string $clientId
	 * @param string $username
	 * @param string $password
	 *
	 * @return void
	 */
	public function create(
		string $host,
		int $port,
		string $clientId,
		string $username = '',
		string $password = ''
	): void
	{
		$client = new MqttClient(
			new ConnectionSettings($host, $port, $clientId, $username, $password),
			$this->handler,
			$this->loop
		);

		try {
			$client->connect()
				->otherwise(function (Throwable $ex) use ($client): void {
					// Log error action reason
					$this->logger->error('[FB:PLUGIN:MQTT] Stopping MQTT client', [
						'exception' => [
							'message' => $ex->getMessage(),
							'code'    => $ex->getCode(),
						],
					]);

					$client->getLoop()
						->stop();
				});

		} catch (Throwable $ex) {
			// Log error action reason
			$this->logger->error('[FB:PLUGIN:MQTT] Stopping MQTT client', [
				'exception' => [
					'message' => $ex->getMessage(),
					'code'    => $ex->getCode(),
				],
			]);

			$client->getLoop()
				->stop();
		}

		$this->client->addClient($client);
	}

}
