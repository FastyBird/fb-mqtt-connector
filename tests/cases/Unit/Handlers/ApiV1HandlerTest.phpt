<?php declare(strict_types = 1);

namespace Tests\Cases;

use BinSoul\Net\Mqtt;
use Exception;
use FastyBird\MqttConnectorPlugin\API;
use FastyBird\MqttConnectorPlugin\Client;
use FastyBird\MqttConnectorPlugin\Consumers;
use FastyBird\MqttConnectorPlugin\Entities;
use FastyBird\MqttConnectorPlugin\Handlers;
use Mockery;
use Nette\Utils;
use Ninjify\Nunjuck\TestCase\BaseMockeryTestCase;
use Psr\Log;
use Ramsey\Uuid;
use React;
use Tester\Assert;

require_once __DIR__ . '/../../../bootstrap.php';

/**
 * @testCase
 */
final class ApiV1HandlerTest extends BaseMockeryTestCase
{

	public function testMqttOnConnectEvent(): void
	{
		$validator = new API\V1Validator();
		$parser = new API\V1Parser($validator);

		$logger = Mockery::mock(Log\LoggerInterface::class);
		$logger
			->shouldReceive('info')
			->withArgs(function (string $message): bool {
				Assert::true(Utils\Strings::startsWith($message, '[FB:PLUGIN:MQTT] Subscribed to: /fb/v1/+'));

				return true;
			});

		$consumer = Mockery::mock(Consumers\Consumer::class);

		$handler = new Handlers\ApiV1Handler($consumer, $validator, $parser, $logger);

		$deferred = new React\Promise\Deferred();

		$connection = Mockery::mock(Mqtt\Connection::class);

		$client = Mockery::mock(Client\MqttClient::class);
		$client
			->shouldReceive('subscribe')
			->withArgs(function (Mqtt\DefaultSubscription $subscription) use ($deferred): bool {
				Assert::true(in_array($subscription->getFilter(), Handlers\ApiV1Handler::DEVICES_TOPICS));

				$deferred->resolve($subscription);

				return true;
			})
			->times(12)
			->andReturn($deferred->promise());

		$handler->onConnect($connection, $client);
	}

	public function testMqttOnConnectFailedEvent(): void
	{
		$validator = new API\V1Validator();
		$parser = new API\V1Parser($validator);

		$logger = Mockery::mock(Log\LoggerInterface::class);
		$logger
			->shouldReceive('error')
			->withArgs(function (string $message): bool {
				Assert::same($message, '[FB:PLUGIN:MQTT] Went wrong');

				return true;
			});

		$consumer = Mockery::mock(Consumers\Consumer::class);

		$handler = new Handlers\ApiV1Handler($consumer, $validator, $parser, $logger);

		$deferred = new React\Promise\Deferred();

		$connection = Mockery::mock(Mqtt\Connection::class);

		$client = Mockery::mock(Client\MqttClient::class);
		$client
			->shouldReceive('subscribe')
			->withArgs(function (Mqtt\DefaultSubscription $topic) use ($deferred): bool {
				$deferred->reject(new Exception('Went wrong'));

				return true;
			})
			->times(12)
			->andReturn($deferred->promise());

		$handler->onConnect($connection, $client);
	}

	public function testMqttOnMessageEvent(): void
	{
		$validator = new API\V1Validator();
		$parser = new API\V1Parser($validator);

		$logger = Mockery::mock(Log\LoggerInterface::class);
		$logger->shouldNotHaveReceived('debug');
		$logger->shouldNotHaveReceived('error');

		$message = new Mqtt\DefaultMessage(
			'/fb/v1/device-name/$name',
			'Custom name'
		);

		$consumer = Mockery::mock(Consumers\Consumer::class);
		$consumer
			->shouldReceive('consume')
			->withArgs(function ($entity): bool {
				Assert::true($entity instanceof Entities\IEntity);

				return true;
			})
			->times(1);

		$handler = new Handlers\ApiV1Handler(
			$consumer,
			$validator,
			$parser,
			$logger
		);

		$client = Mockery::mock(Client\MqttClient::class);
		$client
			->shouldReceive('getClientId')
			->andReturn((string) Uuid\Uuid::uuid4());

		$handler->onMessage($message, $client);
	}

}

$test_case = new ApiV1HandlerTest();
$test_case->run();
