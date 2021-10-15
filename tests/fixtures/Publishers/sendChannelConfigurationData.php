<?php declare(strict_types = 1);

use BinSoul\Net\Mqtt;
use FastyBird\MqttConnectorPlugin;
use Nette\Utils;

$payload = [
	'param_one' => 10,
	'param_two' => 'test',
];

return [
	'one' => [
		new Mqtt\DefaultMessage(
			'/fb/v1/device-name/$channel/channel-name/$control/configure/set',
			Utils\Json::encode($payload),
			MqttConnectorPlugin\Constants::MQTT_API_QOS_1
		),
		'device-name',
		'channel-name',
		Utils\ArrayHash::from($payload),
		null,
	],
	'two' => [
		new Mqtt\DefaultMessage(
			'/fb/v1/parent-name/$child/device-name/$channel/channel-name/$control/configure/set',
			Utils\Json::encode($payload),
			MqttConnectorPlugin\Constants::MQTT_API_QOS_1
		),
		'device-name',
		'channel-name',
		Utils\ArrayHash::from($payload),
		'parent-name',
	],
];
