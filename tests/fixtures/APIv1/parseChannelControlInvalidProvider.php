<?php declare(strict_types = 1);

use FastyBird\MqttPlugin\Entities;
use FastyBird\MqttPlugin\Exceptions;

return [
	'schema-ctrl-' . Entities\Control::RESET       => [
		'/fb/v1/device-name/$channel/channel-name/$control/' . Entities\Control::RESET . '/$schema',
		Exceptions\ParseMessageException::class,
		'Provided topic is not valid',
	],
	'ctrl-unknown'                                 => [
		'/fb/v1/device-name/$channel/channel-name/$control/unknown',
		Exceptions\ParseMessageException::class,
		'Provided topic is not valid',
	],
	'child-schema-ctrl-' . Entities\Control::RESET => [
		'/fb/v1/device-name/$child/child-name/$channel/channel-name/$control/' . Entities\Control::RESET,
		Exceptions\ParseMessageException::class,
		'Provided topic is not valid',
	],
	'child-ctrl-unknown'                           => [
		'/fb/v1/device-name/$child/child-name/$channel/channel-name/$control/unknown',
		Exceptions\ParseMessageException::class,
		'Provided topic is not valid',
	],
];
