<?php declare(strict_types = 1);

namespace Tests\Cases;

use BinSoul\Net\Mqtt;
use FastyBird\MqttPlugin;
use FastyBird\MqttPlugin\API;
use FastyBird\MqttPlugin\Consumers;
use FastyBird\MqttPlugin\Entities;
use FastyBird\MqttPlugin\Events;
use IPub\MQTTClient;
use Mockery;
use Ninjify\Nunjuck\TestCase\BaseMockeryTestCase;
use Psr\Log;
use Tester\Assert;

require_once __DIR__ . '/../../../bootstrap.php';

/**
 * @testCase
 */
final class MqttClientV1MessageHandlerTest extends BaseMockeryTestCase
{

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

		$consumer = Mockery::mock(Consumers\ExchangeConsumer::class);
		$consumer
			->shouldReceive('consume')
			->withArgs(function ($entity): bool {
				Assert::true($entity instanceof Entities\IEntity);

				return true;
			})
			->times(1);

		$subscriber = new Events\MqttClientV1MessageHandler(
			$consumer,
			$validator,
			$parser,
			$logger
		);

		$client = Mockery::mock(MQTTClient\Client\IClient::class);

		$subscriber->__invoke($message, $client);
	}

}

$test_case = new MqttClientV1MessageHandlerTest();
$test_case->run();
