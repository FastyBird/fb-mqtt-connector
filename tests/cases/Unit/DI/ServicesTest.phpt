<?php declare(strict_types = 1);

namespace Tests\Cases;

use FastyBird\MqttPlugin;
use FastyBird\MqttPlugin\API;
use FastyBird\MqttPlugin\Consumers;
use FastyBird\MqttPlugin\Events;
use FastyBird\MqttPlugin\Senders;
use FastyBird\MqttPlugin\Subscribers;
use Tester\Assert;

require_once __DIR__ . '/../../../bootstrap.php';
require_once __DIR__ . '/../BaseTestCase.php';

/**
 * @testCase
 */
final class ServicesTest extends BaseTestCase
{

	public function testServicesRegistration(): void
	{
		$container = $this->createContainer();

		Assert::notNull($container->getByType(API\V1Parser::class));
		Assert::notNull($container->getByType(API\V1Validator::class));

		Assert::notNull($container->getByType(Consumers\ExchangeConsumer::class));

		Assert::notNull($container->getByType(Events\MqttClientCloseHandler::class));
		Assert::notNull($container->getByType(Events\MqttClientConnectHandler::class));
		Assert::notNull($container->getByType(Events\MqttClientDisconnectHandler::class));
		Assert::notNull($container->getByType(Events\MqttClientErrorHandler::class));
		Assert::notNull($container->getByType(Events\MqttClientMessageHandler::class));
		Assert::notNull($container->getByType(Events\MqttClientOpenHandler::class));
		Assert::notNull($container->getByType(Events\MqttClientV1ConnectHandler::class));
		Assert::notNull($container->getByType(Events\MqttClientV1MessageHandler::class));
		Assert::notNull($container->getByType(Events\MqttClientWarningHandler::class));

		Assert::notNull($container->getByType(Senders\MqttV1Sender::class));

		Assert::notNull($container->getByType(Subscribers\ApplicationSubscriber::class));

		Assert::notNull($container->getByType(MqttPlugin\Client::class));
	}

}

$test_case = new ServicesTest();
$test_case->run();
