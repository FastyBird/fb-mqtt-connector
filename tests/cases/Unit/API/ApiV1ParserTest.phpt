<?php declare(strict_types = 1);

namespace Tests\Cases;

use FastyBird\MqttPlugin\API;
use FastyBird\MqttPlugin\Entities;
use Tester\Assert;

require_once __DIR__ . '/../../../bootstrap.php';
require_once __DIR__ . '/../BaseTestCase.php';

/**
 * @testCase
 */
final class ApiV1ParserTest extends BaseTestCase
{

	/**
	 * @param string $topic
	 * @param string $payload
	 * @param mixed[] $expected
	 *
	 * @dataProvider ./../../../fixtures/APIv1/parseDeviceAttributesProvider.php
	 */
	public function testParseDeviceAttribute(
		string $topic,
		string $payload,
		array $expected
	): void {
		/** @var API\V1Parser $apiV1Parser */
		$apiV1Parser = $this->container->getByType(API\V1Parser::class);

		$entity = $apiV1Parser->parse($topic, $payload);

		Assert::true($entity instanceof Entities\DeviceAttribute);
		Assert::equal($expected, $entity->toArray());
	}

	/**
	 * @param string $topic
	 * @param string $payload
	 * @param mixed[] $expected
	 *
	 * @dataProvider ./../../../fixtures/APIv1/parseDeviceHardwareInfoProvider.php
	 */
	public function testParseDeviceHardwareInfo(
		string $topic,
		string $payload,
		array $expected
	): void {
		/** @var API\V1Parser $apiV1Parser */
		$apiV1Parser = $this->container->getByType(API\V1Parser::class);

		$entity = $apiV1Parser->parse($topic, $payload);

		Assert::true($entity instanceof Entities\Hardware);
		Assert::equal($expected, $entity->toArray());
	}

	/**
	 * @param string $topic
	 * @param string $payload
	 * @param mixed[] $expected
	 *
	 * @dataProvider ./../../../fixtures/APIv1/parseDeviceFirmwareInfoProvider.php
	 */
	public function testParseDeviceFirmwareInfo(
		string $topic,
		string $payload,
		array $expected
	): void {
		/** @var API\V1Parser $apiV1Parser */
		$apiV1Parser = $this->container->getByType(API\V1Parser::class);

		$entity = $apiV1Parser->parse($topic, $payload);

		Assert::true($entity instanceof Entities\Firmware);
		Assert::equal($expected, $entity->toArray());
	}

	/**
	 * @param string $topic
	 * @param string $payload
	 * @param mixed[] $expected
	 *
	 * @dataProvider ./../../../fixtures/APIv1/parseDevicePropertiesProvider.php
	 */
	public function testParseDeviceProperties(
		string $topic,
		string $payload,
		array $expected
	): void {
		/** @var API\V1Parser $apiV1Parser */
		$apiV1Parser = $this->container->getByType(API\V1Parser::class);

		$entity = $apiV1Parser->parse($topic, $payload);

		Assert::true($entity instanceof Entities\DeviceProperty);
		Assert::equal($expected, $entity->toArray());
	}

	/**
	 * @param string $topic
	 * @param string $payload
	 * @param mixed[] $expected
	 *
	 * @dataProvider ./../../../fixtures/APIv1/parseDevicePropertiesAttributesProvider.php
	 */
	public function testParseDevicePropertiesAttributes(
		string $topic,
		string $payload,
		array $expected
	): void {
		/** @var API\V1Parser $apiV1Parser */
		$apiV1Parser = $this->container->getByType(API\V1Parser::class);

		$entity = $apiV1Parser->parse($topic, $payload);

		Assert::true($entity instanceof Entities\DeviceProperty);
		Assert::equal($expected, $entity->toArray());
	}

	/**
	 * @param string $topic
	 * @param string $payload
	 * @param mixed[] $expected
	 *
	 * @dataProvider ./../../../fixtures/APIv1/parseDeviceControlProvider.php
	 */
	public function testParseDeviceControl(
		string $topic,
		string $payload,
		array $expected
	): void {
		/** @var API\V1Parser $apiV1Parser */
		$apiV1Parser = $this->container->getByType(API\V1Parser::class);

		$entity = $apiV1Parser->parse($topic, $payload);

		Assert::true($entity instanceof Entities\DeviceControl);
		Assert::equal($expected, $entity->toArray());
	}

	/**
	 * @param string $topic
	 * @param string $exception
	 * @param string $message
	 *
	 * @dataProvider ./../../../fixtures/APIv1/parseDeviceAttributesInvalidProvider.php
	 */
	public function testParseDeviceAttributeNotValid(
		string $topic,
		string $exception,
		string $message
	): void {
		/** @var API\V1Parser $apiV1Parser */
		$apiV1Parser = $this->container->getByType(API\V1Parser::class);

		Assert::exception(function () use ($apiV1Parser, $topic): void {
			$apiV1Parser->parse($topic, 'bar');
		}, $exception, $message);
	}

	/**
	 * @param string $topic
	 * @param string $exception
	 * @param string $message
	 *
	 * @dataProvider ./../../../fixtures/APIv1/parseDeviceHardwareInfoInvalidProvider.php
	 */
	public function testParseDeviceHardwareInfoNotValid(
		string $topic,
		string $exception,
		string $message
	): void {
		/** @var API\V1Parser $apiV1Parser */
		$apiV1Parser = $this->container->getByType(API\V1Parser::class);

		Assert::exception(function () use ($apiV1Parser, $topic): void {
			$apiV1Parser->parse($topic, 'bar');
		}, $exception, $message);
	}

	/**
	 * @param string $topic
	 * @param string $exception
	 * @param string $message
	 *
	 * @dataProvider ./../../../fixtures/APIv1/parseDeviceFirmwareInfoInvalidProvider.php
	 */
	public function testParseDeviceFirmwareInfoNotValid(
		string $topic,
		string $exception,
		string $message
	): void {
		/** @var API\V1Parser $apiV1Parser */
		$apiV1Parser = $this->container->getByType(API\V1Parser::class);

		Assert::exception(function () use ($apiV1Parser, $topic): void {
			$apiV1Parser->parse($topic, 'bar');
		}, $exception, $message);
	}

	/**
	 * @param string $topic
	 * @param string $exception
	 * @param string $message
	 *
	 * @dataProvider ./../../../fixtures/APIv1/parseDeviceControlInvalidProvider.php
	 */
	public function testParseDeviceControlNotValid(
		string $topic,
		string $exception,
		string $message
	): void {
		/** @var API\V1Parser $apiV1Parser */
		$apiV1Parser = $this->container->getByType(API\V1Parser::class);

		Assert::exception(function () use ($apiV1Parser, $topic): void {
			$apiV1Parser->parse($topic, 'bar');
		}, $exception, $message);
	}

	/**
	 * @param string $topic
	 * @param string $payload
	 * @param mixed[] $expected
	 *
	 * @dataProvider ./../../../fixtures/APIv1/parseChannelAttributesProvider.php
	 */
	public function testParseChannelAttributes(
		string $topic,
		string $payload,
		array $expected
	): void {
		/** @var API\V1Parser $apiV1Parser */
		$apiV1Parser = $this->container->getByType(API\V1Parser::class);

		$entity = $apiV1Parser->parse($topic, $payload);

		Assert::true($entity instanceof Entities\ChannelAttribute);
		Assert::equal($expected, $entity->toArray());
	}

	/**
	 * @param string $topic
	 * @param string $payload
	 * @param mixed[] $expected
	 *
	 * @dataProvider ./../../../fixtures/APIv1/parseChannelPropertiesProvider.php
	 */
	public function testParseChannelProperties(
		string $topic,
		string $payload,
		array $expected
	): void {
		/** @var API\V1Parser $apiV1Parser */
		$apiV1Parser = $this->container->getByType(API\V1Parser::class);

		$entity = $apiV1Parser->parse($topic, $payload);

		Assert::true($entity instanceof Entities\ChannelProperty);
		Assert::equal($expected, $entity->toArray());
	}

	/**
	 * @param string $topic
	 * @param string $payload
	 * @param mixed[] $expected
	 *
	 * @dataProvider ./../../../fixtures/APIv1/parseChannelPropertiesAttributesProvider.php
	 */
	public function testParseChannelPropertiesAttributes(
		string $topic,
		string $payload,
		array $expected
	): void {
		/** @var API\V1Parser $apiV1Parser */
		$apiV1Parser = $this->container->getByType(API\V1Parser::class);

		$entity = $apiV1Parser->parse($topic, $payload);

		Assert::true($entity instanceof Entities\ChannelProperty);
		Assert::equal($expected, $entity->toArray());
	}

	/**
	 * @param string $topic
	 * @param string $payload
	 * @param mixed[] $expected
	 *
	 * @dataProvider ./../../../fixtures/APIv1/parseChannelControlProvider.php
	 */
	public function testParseChannelControl(
		string $topic,
		string $payload,
		array $expected
	): void {
		/** @var API\V1Parser $apiV1Parser */
		$apiV1Parser = $this->container->getByType(API\V1Parser::class);

		$entity = $apiV1Parser->parse($topic, $payload);

		Assert::true($entity instanceof Entities\ChannelControl);
		Assert::equal($expected, $entity->toArray());
	}

	/**
	 * @param string $topic
	 * @param string $exception
	 * @param string $message
	 *
	 * @dataProvider ./../../../fixtures/APIv1/parseChannelAttributesInvalidProvider.php
	 */
	public function testParseChannelAttributeNotValid(
		string $topic,
		string $exception,
		string $message
	): void {
		/** @var API\V1Parser $apiV1Parser */
		$apiV1Parser = $this->container->getByType(API\V1Parser::class);

		Assert::exception(function () use ($apiV1Parser, $topic): void {
			$apiV1Parser->parse($topic, 'bar');
		}, $exception, $message);
	}

	/**
	 * @param string $topic
	 * @param string $exception
	 * @param string $message
	 *
	 * @dataProvider ./../../../fixtures/APIv1/parseChannelControlInvalidProvider.php
	 */
	public function testParseChannelControlNotValid(
		string $topic,
		string $exception,
		string $message
	): void {
		/** @var API\V1Parser $apiV1Parser */
		$apiV1Parser = $this->container->getByType(API\V1Parser::class);

		Assert::exception(function () use ($apiV1Parser, $topic): void {
			$apiV1Parser->parse($topic, 'bar');
		}, $exception, $message);
	}

}

$test_case = new ApiV1ParserTest();
$test_case->run();
