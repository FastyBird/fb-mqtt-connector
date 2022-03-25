<?php declare(strict_types = 1);

namespace Tests\Cases;

use FastyBird\FbMqttConnector\API;
use FastyBird\FbMqttConnector\Entities;
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

		Assert::true($apiV1Validator->validate('/fb/v1/device-name/$' . Entities\Messages\Attribute::NAME));
		Assert::true($apiV1Validator->validate('/fb/v1/device-name/$' . Entities\Messages\Attribute::PROPERTIES));
		Assert::true($apiV1Validator->validate('/fb/v1/device-name/$' . Entities\Messages\Attribute::CHANNELS));
		Assert::true($apiV1Validator->validate('/fb/v1/device-name/$' . Entities\Messages\Attribute::EXTENSIONS));
		Assert::true($apiV1Validator->validate('/fb/v1/device-name/$' . Entities\Messages\Attribute::CONTROL));
		Assert::false($apiV1Validator->validate('/fb/v1/device-name/$invalid'));
		Assert::false($apiV1Validator->validate('/fb/v1/device-name/invalid'));
		Assert::false($apiV1Validator->validate('/fb/v1/device-name/$' . Entities\Messages\Attribute::NAME . '/test'));
		Assert::false($apiV1Validator->validate('/fb/v1/device-name/' . Entities\Messages\Attribute::NAME));
		Assert::false($apiV1Validator->validate('/fb/v1/Device-Name/$' . Entities\Messages\Attribute::EXTENSIONS));

		Assert::true($apiV1Validator->validate('/fb/v1/device-name/$hw/' . Entities\Messages\Hardware::MAC_ADDRESS));
		Assert::true($apiV1Validator->validate('/fb/v1/device-name/$hw/' . Entities\Messages\Hardware::MANUFACTURER));
		Assert::true($apiV1Validator->validate('/fb/v1/device-name/$hw/' . Entities\Messages\Hardware::MODEL));
		Assert::true($apiV1Validator->validate('/fb/v1/device-name/$hw/' . Entities\Messages\Hardware::VERSION));
		Assert::false($apiV1Validator->validate('/fb/v1/device-name/$mw/' . Entities\Messages\Hardware::VERSION));
		Assert::false($apiV1Validator->validate('/fb/v1/device-name/$hardware/' . Entities\Messages\Hardware::VERSION));
		Assert::false($apiV1Validator->validate('/fb/v1/device-name/$hware/' . Entities\Messages\Hardware::VERSION));

		Assert::true($apiV1Validator->validate('/fb/v1/device-name/$fw/' . Entities\Messages\Firmware::MANUFACTURER));
		Assert::true($apiV1Validator->validate('/fb/v1/device-name/$fw/' . Entities\Messages\Firmware::VERSION));
		Assert::false($apiV1Validator->validate('/fb/v1/device-name/$mw/' . Entities\Messages\Firmware::VERSION));
		Assert::false($apiV1Validator->validate('/fb/v1/device-name/$firmware/' . Entities\Messages\Firmware::VERSION));
		Assert::false($apiV1Validator->validate('/fb/v1/device-name/$fware/' . Entities\Messages\Firmware::VERSION));

		Assert::false($apiV1Validator->validate('/fb/v1/device-name'));
		Assert::false($apiV1Validator->validate('/fb/v1/$device-name/$' . Entities\Messages\Attribute::NAME));
		Assert::false($apiV1Validator->validate('/fb/v1/device?-name/$' . Entities\Messages\Attribute::NAME));
		Assert::false($apiV1Validator->validate('/fb/v1/dev&ice-name/$' . Entities\Messages\Attribute::NAME));
		Assert::false($apiV1Validator->validate('/fb/v1/dev*ice-name/$' . Entities\Messages\Attribute::NAME));
		Assert::false($apiV1Validator->validate('/fb/v1/device_name/$' . Entities\Messages\Attribute::NAME));
		Assert::false($apiV1Validator->validate('/fb/v1/device.name/$' . Entities\Messages\Attribute::NAME));
	}

	public function testValidateChannels(): void
	{
		$apiV1Validator = new API\V1Validator();

		Assert::true($apiV1Validator->validateChannelPart('/fb/v1/device-name/$channel/channel-name/'));
		Assert::true($apiV1Validator->validateChannelPart('/fb/v1/device-name/$channel/channel-name/whatever'));
		Assert::false($apiV1Validator->validateChannelPart('/fb/v1/device-name/$channel/channel-name'));
		Assert::false($apiV1Validator->validateChannelPart('/fb/v1/device-name/$whatever/channel-name/'));
		Assert::false($apiV1Validator->validateChannelPart('/fb/v1/device-name/channel/channel-name/'));

		Assert::true($apiV1Validator->validateChannelAttribute('/fb/v1/whatever/$channel/channel-name/$' . Entities\Messages\Attribute::NAME));
		Assert::true($apiV1Validator->validateChannelAttribute('/fb/v1/whatever/$channel/channel-name/$' . Entities\Messages\Attribute::PROPERTIES));
		Assert::true($apiV1Validator->validateChannelAttribute('/fb/v1/whatever/$channel/channel-name/$' . Entities\Messages\Attribute::CONTROL));
		Assert::false($apiV1Validator->validateChannelAttribute('/fb/v1/whatever/$channel/channel-name/$unknown'));

		Assert::true($apiV1Validator->validateChannelProperty('/fb/v1/whatever/$channel/channel-name/$property/property-name'));
		Assert::true($apiV1Validator->validateChannelProperty('/fb/v1/whatever/$channel/channel-name/$property/property-name/$' . Entities\Messages\PropertyAttribute::NAME));
		Assert::true($apiV1Validator->validateChannelProperty('/fb/v1/whatever/$channel/channel-name/$property/property-name/$' . Entities\Messages\PropertyAttribute::TYPE));
		Assert::true($apiV1Validator->validateChannelProperty('/fb/v1/whatever/$channel/channel-name/$property/property-name/$' . Entities\Messages\PropertyAttribute::SETTABLE));
		Assert::true($apiV1Validator->validateChannelProperty('/fb/v1/whatever/$channel/channel-name/$property/property-name/$' . Entities\Messages\PropertyAttribute::QUERYABLE));
		Assert::true($apiV1Validator->validateChannelProperty('/fb/v1/whatever/$channel/channel-name/$property/property-name/$' . Entities\Messages\PropertyAttribute::DATATYPE));
		Assert::true($apiV1Validator->validateChannelProperty('/fb/v1/whatever/$channel/channel-name/$property/property-name/$' . Entities\Messages\PropertyAttribute::FORMAT));
		Assert::true($apiV1Validator->validateChannelProperty('/fb/v1/whatever/$channel/channel-name/$property/property-name/$' . Entities\Messages\PropertyAttribute::UNIT));
		Assert::false($apiV1Validator->validateChannelProperty('/fb/v1/whatever/$channel/channel-name/$property/property-name/' . Entities\Messages\PropertyAttribute::UNIT));
		Assert::false($apiV1Validator->validateChannelProperty('/fb/v1/whatever/$channel/channel-name/$property/property-name/$invalid'));
	}

}

$test_case = new ApiV1ValidatorTest();
$test_case->run();
