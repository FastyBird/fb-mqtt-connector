<?php declare(strict_types = 1);

use BinSoul\Net\Mqtt;
use FastyBird\MqttPlugin;

$payload = 'value';

return [
	'one' => [
		new Mqtt\DefaultMessage(
			'/fb/v1/device-name/$channel/channel-name/$property/property-name/set',
			$payload,
			MqttPlugin\Constants::MQTT_API_QOS_1
		),
		'device-name',
		'channel-name',
		'property-name',
		$payload,
		null,
	],
	'two' => [
		new Mqtt\DefaultMessage(
			'/fb/v1/parent-name/$child/device-name/$channel/channel-name/$property/property-name/set',
			$payload,
			MqttPlugin\Constants::MQTT_API_QOS_1
		),
		'device-name',
		'channel-name',
		'property-name',
		$payload,
		'parent-name',
	],
];
