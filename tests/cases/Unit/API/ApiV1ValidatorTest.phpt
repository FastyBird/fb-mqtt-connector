<?php declare(strict_types = 1);

namespace Tests\Cases;

use FastyBird\MqttConnectorPlugin\API;
use FastyBird\MqttConnectorPlugin\Entities;
use Ninjify\Nunjuck\TestCase\BaseTestCase;
use Tester\Assert;

require_once __DIR__ . '/../../../bootstrap.php';

/**
 * @testCase
 */
final class ApiV1ValidatorTest extends BaseTestCase
{

	public function testValidateDevices(): void
	{
		$apiV1Validator = new API\V1Validator();

		Assert::true($apiV1Validator->validateVersion('/fb/v1/device-name/'));
		Assert::false($apiV1Validator->validateVersion('/fb/v2/device-name/'));
		Assert::false($apiV1Validator->validateVersion('/v1/device-name/'));
		Assert::false($apiV1Validator->validateVersion('/v2/device-name/'));

		Assert::true($apiV1Validator->validate('/fb/v1/device-name/$' . Entities\Attribute::NAME));
		Assert::true($apiV1Validator->validate('/fb/v1/device-name/$' . Entities\Attribute::PROPERTIES));
		Assert::true($apiV1Validator->validate('/fb/v1/device-name/$' . Entities\Attribute::CHANNELS));
		Assert::true($apiV1Validator->validate('/fb/v1/device-name/$' . Entities\Attribute::EXTENSIONS));
		Assert::true($apiV1Validator->validate('/fb/v1/device-name/$' . Entities\Attribute::CONTROL));
		Assert::false($apiV1Validator->validate('/fb/v1/device-name/$invalid'));
		Assert::false($apiV1Validator->validate('/fb/v1/device-name/invalid'));
		Assert::false($apiV1Validator->validate('/fb/v1/device-name/$' . Entities\Attribute::NAME . '/test'));
		Assert::false($apiV1Validator->validate('/fb/v1/device-name/' . Entities\Attribute::NAME));
		Assert::false($apiV1Validator->validate('/fb/v1/Device-Name/$' . Entities\Attribute::EXTENSIONS));

		Assert::true($apiV1Validator->validate('/fb/v1/device-name/$child/child-name/$' . Entities\Attribute::NAME));
		Assert::true($apiV1Validator->validate('/fb/v1/device-name/$child/child-name/$' . Entities\Attribute::PROPERTIES));
		Assert::true($apiV1Validator->validate('/fb/v1/device-name/$child/child-name/$' . Entities\Attribute::CHANNELS));
		Assert::true($apiV1Validator->validate('/fb/v1/device-name/$child/child-name/$' . Entities\Attribute::EXTENSIONS));
		Assert::true($apiV1Validator->validate('/fb/v1/device-name/$child/child-name/$' . Entities\Attribute::CONTROL));
		Assert::false($apiV1Validator->validate('/fb/v1/device-name/$child/child-name/$invalid'));
		Assert::false($apiV1Validator->validate('/fb/v1/device-name/$child/child-name/invalid'));
		Assert::false($apiV1Validator->validate('/fb/v1/device-name/$child/child-name/$' . Entities\Attribute::NAME . '/next'));
		Assert::false($apiV1Validator->validate('/fb/v1/device-name/$child/child-name/' . Entities\Attribute::NAME));
		Assert::false($apiV1Validator->validate('/fb/v1/device-name/$child/child-*name/$' . Entities\Attribute::CONTROL));
		Assert::false($apiV1Validator->validate('/fb/v1/device-name/child/child-name/$' . Entities\Attribute::CONTROL));
		Assert::false($apiV1Validator->validate('/fb/v1/device-name/$chuld/child-name/$' . Entities\Attribute::CONTROL));
		Assert::false($apiV1Validator->validate('/fb/v1/device-name/$child/chilD-Name/$' . Entities\Attribute::CONTROL));

		Assert::true($apiV1Validator->validate('/fb/v1/device-name/$hw/' . Entities\Hardware::MAC_ADDRESS));
		Assert::true($apiV1Validator->validate('/fb/v1/device-name/$hw/' . Entities\Hardware::MANUFACTURER));
		Assert::true($apiV1Validator->validate('/fb/v1/device-name/$hw/' . Entities\Hardware::MODEL));
		Assert::true($apiV1Validator->validate('/fb/v1/device-name/$hw/' . Entities\Hardware::VERSION));
		Assert::false($apiV1Validator->validate('/fb/v1/device-name/$mw/' . Entities\Hardware::VERSION));
		Assert::false($apiV1Validator->validate('/fb/v1/device-name/$hardware/' . Entities\Hardware::VERSION));
		Assert::false($apiV1Validator->validate('/fb/v1/device-name/$hware/' . Entities\Hardware::VERSION));

		Assert::true($apiV1Validator->validate('/fb/v1/device-name/$fw/' . Entities\Firmware::MANUFACTURER));
		Assert::true($apiV1Validator->validate('/fb/v1/device-name/$fw/' . Entities\Firmware::NAME));
		Assert::true($apiV1Validator->validate('/fb/v1/device-name/$fw/' . Entities\Firmware::VERSION));
		Assert::false($apiV1Validator->validate('/fb/v1/device-name/$mw/' . Entities\Firmware::VERSION));
		Assert::false($apiV1Validator->validate('/fb/v1/device-name/$firmware/' . Entities\Firmware::VERSION));
		Assert::false($apiV1Validator->validate('/fb/v1/device-name/$fware/' . Entities\Firmware::VERSION));

		Assert::true($apiV1Validator->validate('/fb/v1/device-name/$child/child-name/$hw/' . Entities\Hardware::MAC_ADDRESS));
		Assert::true($apiV1Validator->validate('/fb/v1/device-name/$child/child-name/$fw/' . Entities\Firmware::MANUFACTURER));
		Assert::false($apiV1Validator->validate('/fb/v1/device-name/$child/child-name/hw/' . Entities\Hardware::MAC_ADDRESS));
		Assert::false($apiV1Validator->validate('/fb/v1/device-name/$child/child-name/fw/' . Entities\Firmware::MANUFACTURER));

		Assert::false($apiV1Validator->validate('/fb/v1/device-name'));
		Assert::false($apiV1Validator->validate('/fb/v1/$device-name/$' . Entities\Attribute::NAME));
		Assert::false($apiV1Validator->validate('/fb/v1/device?-name/$' . Entities\Attribute::NAME));
		Assert::false($apiV1Validator->validate('/fb/v1/dev&ice-name/$' . Entities\Attribute::NAME));
		Assert::false($apiV1Validator->validate('/fb/v1/dev*ice-name/$' . Entities\Attribute::NAME));
		Assert::false($apiV1Validator->validate('/fb/v1/device_name/$' . Entities\Attribute::NAME));
		Assert::false($apiV1Validator->validate('/fb/v1/device.name/$' . Entities\Attribute::NAME));
	}

	public function testValidateChannels(): void
	{
		$apiV1Validator = new API\V1Validator();

		Assert::true($apiV1Validator->validateChannelPart('/fb/v1/device-name/$channel/channel-name/'));
		Assert::true($apiV1Validator->validateChannelPart('/fb/v1/device-name/$channel/channel-name/whatever'));
		Assert::false($apiV1Validator->validateChannelPart('/fb/v1/device-name/$channel/channel-name'));
		Assert::false($apiV1Validator->validateChannelPart('/fb/v1/device-name/$whatever/channel-name/'));
		Assert::false($apiV1Validator->validateChannelPart('/fb/v1/device-name/channel/channel-name/'));

		Assert::true($apiV1Validator->validateChannelAttribute('/fb/v1/whatever/$channel/channel-name/$' . Entities\Attribute::NAME));
		Assert::true($apiV1Validator->validateChannelAttribute('/fb/v1/whatever/$channel/channel-name/$' . Entities\Attribute::PROPERTIES));
		Assert::true($apiV1Validator->validateChannelAttribute('/fb/v1/whatever/$channel/channel-name/$' . Entities\Attribute::CONTROL));
		Assert::false($apiV1Validator->validateChannelAttribute('/fb/v1/whatever/$channel/channel-name/$unknown'));

		Assert::true($apiV1Validator->validate('/fb/v1/device-name/$child/child-name/$channel/channel-name/$' . Entities\Attribute::NAME));
		Assert::true($apiV1Validator->validate('/fb/v1/device-name/$child/child-name/$channel/channel-name/$' . Entities\Attribute::PROPERTIES));
		Assert::true($apiV1Validator->validate('/fb/v1/device-name/$child/child-name/$channel/channel-name/$' . Entities\Attribute::CONTROL));
		Assert::false($apiV1Validator->validate('/fb/v1/device-name/$child/child-name/$channel/channel-name/$unknown'));
		Assert::false($apiV1Validator->validate('/fb/v1/device-name/$child/child-name/$channel/channel-name/$' . Entities\Attribute::NAME . '/next'));

		Assert::true($apiV1Validator->validateChannelProperty('/fb/v1/whatever/$channel/channel-name/$property/property-name'));
		Assert::true($apiV1Validator->validateChannelProperty('/fb/v1/whatever/$channel/channel-name/$property/property-name/$' . Entities\PropertyAttribute::NAME));
		Assert::true($apiV1Validator->validateChannelProperty('/fb/v1/whatever/$channel/channel-name/$property/property-name/$' . Entities\PropertyAttribute::TYPE));
		Assert::true($apiV1Validator->validateChannelProperty('/fb/v1/whatever/$channel/channel-name/$property/property-name/$' . Entities\PropertyAttribute::SETTABLE));
		Assert::true($apiV1Validator->validateChannelProperty('/fb/v1/whatever/$channel/channel-name/$property/property-name/$' . Entities\PropertyAttribute::QUERYABLE));
		Assert::true($apiV1Validator->validateChannelProperty('/fb/v1/whatever/$channel/channel-name/$property/property-name/$' . Entities\PropertyAttribute::DATATYPE));
		Assert::true($apiV1Validator->validateChannelProperty('/fb/v1/whatever/$channel/channel-name/$property/property-name/$' . Entities\PropertyAttribute::FORMAT));
		Assert::true($apiV1Validator->validateChannelProperty('/fb/v1/whatever/$channel/channel-name/$property/property-name/$' . Entities\PropertyAttribute::UNIT));
		Assert::false($apiV1Validator->validateChannelProperty('/fb/v1/whatever/$channel/channel-name/$property/property-name/' . Entities\PropertyAttribute::UNIT));
		Assert::false($apiV1Validator->validateChannelProperty('/fb/v1/whatever/$channel/channel-name/$property/property-name/$invalid'));

		Assert::true($apiV1Validator->validate('/fb/v1/device-name/$child/child-name/$channel/channel-name/$property/property-name'));
		Assert::true($apiV1Validator->validate('/fb/v1/device-name/$child/child-name/$channel/channel-name/$property/property-name/$' . Entities\PropertyAttribute::NAME));
		Assert::true($apiV1Validator->validate('/fb/v1/device-name/$child/child-name/$channel/channel-name/$property/property-name/$' . Entities\PropertyAttribute::TYPE));
		Assert::true($apiV1Validator->validate('/fb/v1/device-name/$child/child-name/$channel/channel-name/$property/property-name/$' . Entities\PropertyAttribute::SETTABLE));
		Assert::true($apiV1Validator->validate('/fb/v1/device-name/$child/child-name/$channel/channel-name/$property/property-name/$' . Entities\PropertyAttribute::QUERYABLE));
		Assert::true($apiV1Validator->validate('/fb/v1/device-name/$child/child-name/$channel/channel-name/$property/property-name/$' . Entities\PropertyAttribute::DATATYPE));
		Assert::true($apiV1Validator->validate('/fb/v1/device-name/$child/child-name/$channel/channel-name/$property/property-name/$' . Entities\PropertyAttribute::FORMAT));
		Assert::true($apiV1Validator->validate('/fb/v1/device-name/$child/child-name/$channel/channel-name/$property/property-name/$' . Entities\PropertyAttribute::UNIT));
		Assert::false($apiV1Validator->validate('/fb/v1/device-name/$child/child-name/$channel/channel-name/$property/property-name/' . Entities\PropertyAttribute::UNIT));
		Assert::false($apiV1Validator->validate('/fb/v1/device-name/$child/child-name/$channel/channel-name/$property/property-name/$invalid'));

		Assert::true($apiV1Validator->validateChannelControl('/fb/v1/whatever/$channel/channel-name/$control/' . Entities\Control::CONFIG));
		Assert::true($apiV1Validator->validateChannelControl('/fb/v1/whatever/$channel/channel-name/$control/' . Entities\Control::CONFIG . '/$schema'));
		Assert::false($apiV1Validator->validateChannelControl('/fb/v1/whatever/$channel/channel-name/$control/invalid/$schema'));
		Assert::false($apiV1Validator->validateChannelControl('/fb/v1/whatever/$channel/channel-name/$control/' . Entities\Control::CONFIG . '/schema'));
	}

}

$test_case = new ApiV1ValidatorTest();
$test_case->run();
