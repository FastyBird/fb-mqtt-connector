<?php declare(strict_types = 1);

/**
 * ApplicationSubscriber.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:MqttPlugin!
 * @subpackage     Subscribers
 * @since          0.1.0
 *
 * @date           21.12.20
 */

namespace FastyBird\MqttPlugin\Subscribers;

use FastyBird\ApplicationEvents\Events as ApplicationEventsEvents;
use FastyBird\MqttPlugin;
use Symfony\Component\EventDispatcher;

/**
 * Server startup subscriber
 *
 * @package         FastyBird:MqttPlugin!
 * @subpackage      Subscribers
 *
 * @author          Adam Kadlec <adam.kadlec@fastybird.com>
 */
class ApplicationSubscriber implements EventDispatcher\EventSubscriberInterface
{

	/** @var MqttPlugin\Client */
	private MqttPlugin\Client $mqttClient;

	/**
	 * @return string[]
	 */
	public static function getSubscribedEvents(): array
	{
		return [
			ApplicationEventsEvents\StartupEvent::class  => 'initialize',
		];
	}

	public function __construct(
		MqttPlugin\Client $mqttClient
	) {
		$this->mqttClient = $mqttClient;
	}

	/**
	 * @return void
	 */
	public function initialize(): void
	{
		$this->mqttClient->connect();
	}

}
