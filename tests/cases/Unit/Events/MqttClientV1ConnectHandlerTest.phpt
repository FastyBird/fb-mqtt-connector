<?php declare(strict_types = 1);

namespace Tests\Cases;

use BinSoul\Net\Mqtt;
use Exception;
use FastyBird\MqttPlugin\Events;
use IPub\MQTTClient;
use Mockery;
use Nette\Utils;
use Ninjify\Nunjuck\TestCase\BaseMockeryTestCase;
use Psr\Log;
use React;
use Tester\Assert;

require_once __DIR__ . '/../../../bootstrap.php';

/**
 * @testCase
 */
final class MqttClientV1ConnectHandlerTest extends BaseMockeryTestCase
{

	public function testMqttOnConnectEvent(): void
	{
		$logger = Mockery::mock(Log\LoggerInterface::class);
		$logger
			->shouldReceive('info')
			->withArgs(function (string $message): bool {
				Assert::true(Utils\Strings::startsWith($message, '[FB:PLUGIN:MQTT] Subscribed to: /fb/v1/+'));

				return true;
			});

		$subscriber = new Events\MqttClientV1ConnectHandler($logger);

		$deferred = new React\Promise\Deferred();

		$connection = Mockery::mock(Mqtt\Connection::class);

		$client = Mockery::mock(MQTTClient\Client\IClient::class);
		$client
			->shouldReceive('subscribe')
			->withArgs(function (Mqtt\DefaultSubscription $subscription) use ($deferred): bool {
				Assert::true(in_array($subscription->getFilter(), Events\MqttClientV1ConnectHandler::DEVICES_TOPICS));

				$deferred->resolve($subscription);

				return true;
			})
			->times(12)
			->andReturn($deferred->promise());

		$subscriber->__invoke($connection, $client);
	}

	public function testMqttOnConnectFailedEvent(): void
	{
		$logger = Mockery::mock(Log\LoggerInterface::class);
		$logger
			->shouldReceive('error')
			->withArgs(function (string $message): bool {
				Assert::same($message, '[FB:PLUGIN:MQTT] Went wrong');

				return true;
			});

		$subscriber = new Events\MqttClientV1ConnectHandler($logger);

		$deferred = new React\Promise\Deferred();

		$connection = Mockery::mock(Mqtt\Connection::class);

		$client = Mockery::mock(MQTTClient\Client\IClient::class);
		$client
			->shouldReceive('subscribe')
			->withArgs(function (Mqtt\DefaultSubscription $topic) use ($deferred): bool {
				$deferred->reject(new Exception('Went wrong'));

				return true;
			})
			->times(12)
			->andReturn($deferred->promise());

		$subscriber->__invoke($connection, $client);
	}

}

$test_case = new MqttClientV1ConnectHandlerTest();
$test_case->run();
