<?php declare(strict_types = 1);

use BinSoul\Net\Mqtt;
use FastyBird\MqttPlugin;

return [
	'one' => [
		new Mqtt\DefaultMessage(
			'/fb/v1/device-name/$control/factory-reset/set',
			'true',
			MqttPlugin\Constants::MQTT_API_QOS_1
		),
		'device-name',
		null,
	],
	'two' => [
		new Mqtt\DefaultMessage(
			'/fb/v1/parent-name/$child/device-name/$control/factory-reset/set',
			'true',
			MqttPlugin\Constants::MQTT_API_QOS_1
		),
		'device-name',
		'parent-name',
	],
];
