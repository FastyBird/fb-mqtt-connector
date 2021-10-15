<?php declare(strict_types = 1);

use BinSoul\Net\Mqtt;
use FastyBird\MqttConnectorPlugin;

return [
	'one' => [
		new Mqtt\DefaultMessage(
			'/fb/v1/device-name/$control/reconnect/set',
			'true',
			MqttConnectorPlugin\Constants::MQTT_API_QOS_1
		),
		'device-name',
		null,
	],
	'two' => [
		new Mqtt\DefaultMessage(
			'/fb/v1/parent-name/$child/device-name/$control/reconnect/set',
			'true',
			MqttConnectorPlugin\Constants::MQTT_API_QOS_1
		),
		'device-name',
		'parent-name',
	],
];
