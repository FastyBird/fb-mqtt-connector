<?php declare(strict_types = 1);

namespace Tests\Cases;

use BinSoul\Net\Mqtt;
use FastyBird\MqttConnectorPlugin\Client;
use FastyBird\MqttConnectorPlugin\Publishers;
use Mockery;
use Nette\Utils;
use Ninjify\Nunjuck\TestCase\BaseMockeryTestCase;
use Psr\Log;
use Tester\Assert;

require_once __DIR__ . '/../../../bootstrap.php';

/**
 * @testCase
 */
final class ApiV1PublisherTest extends BaseMockeryTestCase
{

	/**
	 * @param Mqtt\DefaultMessage $expectedMessage
	 * @param string $device
	 * @param string $channel
	 * @param string $property
	 * @param string|null $payload
	 * @param string|null $parentDevice
	 *
	 * @dataProvider ./../../../fixtures/Publishers/sendChannelPropertyData.php
	 */
	public function testSendChannelProperty(
		Mqtt\DefaultMessage $expectedMessage,
		string $device,
		string $channel,
		string $property,
		?string $payload,
		?string $parentDevice = null
	): void {
		$client = Mockery::mock(Client\Client::class);
		$client
			->shouldReceive('publish')
			->withArgs(function (string $topic, ?string $payload, int $qos, bool $retained) use ($expectedMessage): bool {
				Assert::same($expectedMessage->getTopic(), $topic);
				Assert::same($expectedMessage->getPayload(), $payload);
				Assert::same($expectedMessage->getQosLevel(), $qos);
				Assert::same($expectedMessage->isRetained(), $retained);

				return true;
			})
			->once();

		$logger = Mockery::mock(Log\LoggerInterface::class);
		$logger
			->shouldReceive('info');

		$sender = new Publishers\ApiV1Publisher($client, $logger);
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
	 * @dataProvider ./../../../fixtures/Publishers/sendChannelConfigurationData.php
	 */
	public function testSendChannelConfiguration(
		Mqtt\DefaultMessage $expectedMessage,
		string $device,
		string $channel,
		Utils\ArrayHash $payload,
		?string $parentDevice = null
	): void {
		$client = Mockery::mock(Client\Client::class);
		$client
			->shouldReceive('publish')
			->withArgs(function (string $topic, ?string $payload, int $qos, bool $retained) use ($expectedMessage): bool {
				Assert::same($expectedMessage->getTopic(), $topic);
				Assert::same($expectedMessage->getPayload(), $payload);
				Assert::same($expectedMessage->getQosLevel(), $qos);
				Assert::same($expectedMessage->isRetained(), $retained);

				return true;
			})
			->once();

		$logger = Mockery::mock(Log\LoggerInterface::class);
		$logger
			->shouldReceive('info');

		$sender = new Publishers\ApiV1Publisher($client, $logger);
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
	 * @dataProvider ./../../../fixtures/Publishers/sendDeviceConfigurationData.php
	 */
	public function testSendDeviceConfiguration(
		Mqtt\DefaultMessage $expectedMessage,
		string $device,
		Utils\ArrayHash $payload,
		?string $parentDevice = null
	): void {
		$client = Mockery::mock(Client\Client::class);
		$client
			->shouldReceive('publish')
			->withArgs(function (string $topic, ?string $payload, int $qos, bool $retained) use ($expectedMessage): bool {
				Assert::same($expectedMessage->getTopic(), $topic);
				Assert::same($expectedMessage->getPayload(), $payload);
				Assert::same($expectedMessage->getQosLevel(), $qos);
				Assert::same($expectedMessage->isRetained(), $retained);

				return true;
			})
			->once();

		$logger = Mockery::mock(Log\LoggerInterface::class);
		$logger
			->shouldReceive('info');

		$sender = new Publishers\ApiV1Publisher($client, $logger);
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
	 * @dataProvider ./../../../fixtures/Publishers/sendDeviceRestartCmdData.php
	 */
	public function testSendDeviceRestart(
		Mqtt\DefaultMessage $expectedMessage,
		string $device,
		?string $parentDevice = null
	): void {
		$client = Mockery::mock(Client\Client::class);
		$client
			->shouldReceive('publish')
			->withArgs(function (string $topic, ?string $payload, int $qos, bool $retained) use ($expectedMessage): bool {
				Assert::same($expectedMessage->getTopic(), $topic);
				Assert::same($expectedMessage->getPayload(), $payload);
				Assert::same($expectedMessage->getQosLevel(), $qos);
				Assert::same($expectedMessage->isRetained(), $retained);

				return true;
			})
			->once();

		$logger = Mockery::mock(Log\LoggerInterface::class);
		$logger
			->shouldReceive('info');

		$sender = new Publishers\ApiV1Publisher($client, $logger);
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
	 * @dataProvider ./../../../fixtures/Publishers/sendDeviceReconnectCmdData.php
	 */
	public function testSendDeviceReconnect(
		Mqtt\DefaultMessage $expectedMessage,
		string $device,
		?string $parentDevice = null
	): void {
		$client = Mockery::mock(Client\Client::class);
		$client
			->shouldReceive('publish')
			->withArgs(function (string $topic, ?string $payload, int $qos, bool $retained) use ($expectedMessage): bool {
				Assert::same($expectedMessage->getTopic(), $topic);
				Assert::same($expectedMessage->getPayload(), $payload);
				Assert::same($expectedMessage->getQosLevel(), $qos);
				Assert::same($expectedMessage->isRetained(), $retained);

				return true;
			})
			->once();

		$logger = Mockery::mock(Log\LoggerInterface::class);
		$logger
			->shouldReceive('info');

		$sender = new Publishers\ApiV1Publisher($client, $logger);
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
	 * @dataProvider ./../../../fixtures/Publishers/sendDeviceFactoryResetCmdData.php
	 */
	public function testSendDeviceFactoryReset(
		Mqtt\DefaultMessage $expectedMessage,
		string $device,
		?string $parentDevice = null
	): void {
		$client = Mockery::mock(Client\Client::class);
		$client
			->shouldReceive('publish')
			->withArgs(function (string $topic, ?string $payload, int $qos, bool $retained) use ($expectedMessage): bool {
				Assert::same($expectedMessage->getTopic(), $topic);
				Assert::same($expectedMessage->getPayload(), $payload);
				Assert::same($expectedMessage->getQosLevel(), $qos);
				Assert::same($expectedMessage->isRetained(), $retained);

				return true;
			})
			->once();

		$logger = Mockery::mock(Log\LoggerInterface::class);
		$logger
			->shouldReceive('info');

		$sender = new Publishers\ApiV1Publisher($client, $logger);
		$sender->sendDeviceFactoryReset(
			$device,
			$parentDevice
		);
	}

}

$test_case = new ApiV1PublisherTest();
$test_case->run();
