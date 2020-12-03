<?php declare(strict_types = 1);

namespace Tests\Cases;

use BinSoul\Net\Mqtt;
use FastyBird\MqttPlugin\Senders;
use IPub\MQTTClient;
use Mockery;
use Nette\Utils;
use Ninjify\Nunjuck\TestCase\BaseMockeryTestCase;
use Psr\Log;
use Tester\Assert;

require_once __DIR__ . '/../../../bootstrap.php';

/**
 * @testCase
 */
final class MqttV1SenderTest extends BaseMockeryTestCase
{

	/**
	 * @param Mqtt\DefaultMessage $expectedMessage
	 * @param string $device
	 * @param string $channel
	 * @param string $property
	 * @param string|null $payload
	 * @param string|null $parentDevice
	 *
	 * @dataProvider ./../../../fixtures/Senders/sendChannelPropertyData.php
	 */
	public function testSendChannelProperty(
		Mqtt\DefaultMessage $expectedMessage,
		string $device,
		string $channel,
		string $property,
		?string $payload,
		?string $parentDevice = null
	): void {
		$client = Mockery::mock(MQTTClient\Client\IClient::class);
		$client
			->shouldReceive('publish')
			->withArgs(function (Mqtt\DefaultMessage $message) use ($expectedMessage): bool {
				Assert::same($expectedMessage->getTopic(), $message->getTopic());
				Assert::same($expectedMessage->getPayload(), $message->getPayload());
				Assert::same($expectedMessage->getQosLevel(), $message->getQosLevel());

				return true;
			})
			->once();

		$logger = Mockery::mock(Log\LoggerInterface::class);
		$logger
			->shouldReceive('info');

		$sender = new Senders\MqttV1Sender($client, $logger);
		$sender->sendChannelProperty(
			$device,
			$channel,
			$property,
			$payload,
			$parentDevice
		);
	}

	/**
	 * @param Mqtt\DefaultMessage $expectedMessage
	 * @param string $device
	 * @param string $channel
	 * @param Utils\ArrayHash $payload
	 * @param string|null $parentDevice
	 *
	 * @dataProvider ./../../../fixtures/Senders/sendChannelConfigurationData.php
	 */
	public function testSendChannelConfiguration(
		Mqtt\DefaultMessage $expectedMessage,
		string $device,
		string $channel,
		Utils\ArrayHash $payload,
		?string $parentDevice = null
	): void {
		$client = Mockery::mock(MQTTClient\Client\IClient::class);
		$client
			->shouldReceive('publish')
			->withArgs(function (Mqtt\DefaultMessage $message) use ($expectedMessage): bool {
				Assert::same($expectedMessage->getTopic(), $message->getTopic());
				Assert::same($expectedMessage->getPayload(), $message->getPayload());
				Assert::same($expectedMessage->getQosLevel(), $message->getQosLevel());

				return true;
			})
			->once();

		$logger = Mockery::mock(Log\LoggerInterface::class);
		$logger
			->shouldReceive('info');

		$sender = new Senders\MqttV1Sender($client, $logger);
		$sender->sendChannelConfiguration(
			$device,
			$channel,
			$payload,
			$parentDevice
		);
	}

	/**
	 * @param Mqtt\DefaultMessage $expectedMessage
	 * @param string $device
	 * @param Utils\ArrayHash $payload
	 * @param string|null $parentDevice
	 *
	 * @dataProvider ./../../../fixtures/Senders/sendDeviceConfigurationData.php
	 */
	public function testSendDeviceConfiguration(
		Mqtt\DefaultMessage $expectedMessage,
		string $device,
		Utils\ArrayHash $payload,
		?string $parentDevice = null
	): void {
		$client = Mockery::mock(MQTTClient\Client\IClient::class);
		$client
			->shouldReceive('publish')
			->withArgs(function (Mqtt\DefaultMessage $message) use ($expectedMessage): bool {
				Assert::same($expectedMessage->getTopic(), $message->getTopic());
				Assert::same($expectedMessage->getPayload(), $message->getPayload());
				Assert::same($expectedMessage->getQosLevel(), $message->getQosLevel());

				return true;
			})
			->once();

		$logger = Mockery::mock(Log\LoggerInterface::class);
		$logger
			->shouldReceive('info');

		$sender = new Senders\MqttV1Sender($client, $logger);
		$sender->sendDeviceConfiguration(
			$device,
			$payload,
			$parentDevice
		);
	}

	/**
	 * @param Mqtt\DefaultMessage $expectedMessage
	 * @param string $device
	 * @param string|null $parentDevice
	 *
	 * @dataProvider ./../../../fixtures/Senders/sendDeviceRestartCmdData.php
	 */
	public function testSendDeviceRestart(
		Mqtt\DefaultMessage $expectedMessage,
		string $device,
		?string $parentDevice = null
	): void {
		$client = Mockery::mock(MQTTClient\Client\IClient::class);
		$client
			->shouldReceive('publish')
			->withArgs(function (Mqtt\DefaultMessage $message) use ($expectedMessage): bool {
				Assert::same($expectedMessage->getTopic(), $message->getTopic());
				Assert::same($expectedMessage->getPayload(), $message->getPayload());
				Assert::same($expectedMessage->getQosLevel(), $message->getQosLevel());

				return true;
			})
			->once();

		$logger = Mockery::mock(Log\LoggerInterface::class);
		$logger
			->shouldReceive('info');

		$sender = new Senders\MqttV1Sender($client, $logger);
		$sender->sendDeviceRestart(
			$device,
			$parentDevice
		);
	}

	/**
	 * @param Mqtt\DefaultMessage $expectedMessage
	 * @param string $device
	 * @param string|null $parentDevice
	 *
	 * @dataProvider ./../../../fixtures/Senders/sendDeviceReconnectCmdData.php
	 */
	public function testSendDeviceReconnect(
		Mqtt\DefaultMessage $expectedMessage,
		string $device,
		?string $parentDevice = null
	): void {
		$client = Mockery::mock(MQTTClient\Client\IClient::class);
		$client
			->shouldReceive('publish')
			->withArgs(function (Mqtt\DefaultMessage $message) use ($expectedMessage): bool {
				Assert::same($expectedMessage->getTopic(), $message->getTopic());
				Assert::same($expectedMessage->getPayload(), $message->getPayload());
				Assert::same($expectedMessage->getQosLevel(), $message->getQosLevel());

				return true;
			})
			->once();

		$logger = Mockery::mock(Log\LoggerInterface::class);
		$logger
			->shouldReceive('info');

		$sender = new Senders\MqttV1Sender($client, $logger);
		$sender->sendDeviceReconnect(
			$device,
			$parentDevice
		);
	}

	/**
	 * @param Mqtt\DefaultMessage $expectedMessage
	 * @param string $device
	 * @param string|null $parentDevice
	 *
	 * @dataProvider ./../../../fixtures/Senders/sendDeviceFactoryResetCmdData.php
	 */
	public function testSendDeviceFactoryReset(
		Mqtt\DefaultMessage $expectedMessage,
		string $device,
		?string $parentDevice = null
	): void {
		$client = Mockery::mock(MQTTClient\Client\IClient::class);
		$client
			->shouldReceive('publish')
			->withArgs(function (Mqtt\DefaultMessage $message) use ($expectedMessage): bool {
				Assert::same($expectedMessage->getTopic(), $message->getTopic());
				Assert::same($expectedMessage->getPayload(), $message->getPayload());
				Assert::same($expectedMessage->getQosLevel(), $message->getQosLevel());

				return true;
			})
			->once();

		$logger = Mockery::mock(Log\LoggerInterface::class);
		$logger
			->shouldReceive('info');

		$sender = new Senders\MqttV1Sender($client, $logger);
		$sender->sendDeviceFactoryReset(
			$device,
			$parentDevice
		);
	}

}

$test_case = new MqttV1SenderTest();
$test_case->run();
