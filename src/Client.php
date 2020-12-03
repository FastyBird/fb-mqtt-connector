<?php declare(strict_types = 1);

/**
 * Client.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:MqttPlugin!
 * @subpackage     common
 * @since          0.1.0
 *
 * @date           23.02.20
 */

namespace FastyBird\MqttPlugin;

use IPub\MQTTClient;
use Nette;
use Psr\Log;
use React\EventLoop;
use Throwable;

/**
 * MQTT client
 *
 * @package        FastyBird:MqttPlugin!
 * @subpackage     common
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class Client
{

	use Nette\SmartObject;

	/** @var MQTTClient\Client\IClient */
	private $mqttClient;

	/** @var EventLoop\LoopInterface */
	private $loop;

	/** @var Log\LoggerInterface */
	private $logger;

	public function __construct(
		MQTTClient\Client\IClient $mqttClient,
		EventLoop\LoopInterface $loop,
		?Log\LoggerInterface $logger = null
	) {
		$this->mqttClient = $mqttClient;
		$this->loop = $loop;
		$this->logger = $logger ?? new Log\NullLogger();
	}

	/**
	 * @return void
	 */
	public function connect(): void
	{
		try {
			// Prepare client connection
			$this->mqttClient->connect()
				->otherwise(function (Throwable $ex): void {
					// Log error action reason
					$this->logger->error('[ERROR] FB MQTT node - MQTT client', [
						'exception' => [
							'message' => $ex->getMessage(),
							'code'    => $ex->getCode(),
						],
					]);

					$this->mqttClient->getLoop()->stop();
				});

		} catch (Throwable $ex) {
			// Log error action reason
			$this->logger->error('[ERROR] FB MQTT node - MQTT client', [
				'exception' => [
					'message' => $ex->getMessage(),
					'code'    => $ex->getCode(),
				],
			]);

			$this->loop->stop();
		}
	}

}
