<?php declare(strict_types = 1);

use FastyBird\MqttPlugin\Entities;
use FastyBird\MqttPlugin\Exceptions;

return [
	'ctrl-' . Entities\Control::RESET               => [
		'/fb/v1/device-name/$control/' . Entities\Control::RESET . '/$schema',
		Exceptions\ParseMessageException::class,
		'Provided topic is not valid',
	],
	'ctrl-' . Entities\Control::RECONNECT           => [
		'/fb/v1/device-name/$control/' . Entities\Control::RECONNECT . '/$schema',
		Exceptions\ParseMessageException::class,
		'Provided topic is not valid',
	],
	'ctrl-' . Entities\Control::FACTORY_RESET       => [
		'/fb/v1/device-name/$control/' . Entities\Control::FACTORY_RESET . '/$schema',
		Exceptions\ParseMessageException::class,
		'Provided topic is not valid',
	],
	'ctrl-unknown'                                  => [
		'/fb/v1/device-name/$control/unknown',
		Exceptions\ParseMessageException::class,
		'Provided topic is not valid',
	],
	'child-ctrl-' . Entities\Control::RESET         => [
		'/fb/v1/device-name/$child/child-name/$control/' . Entities\Control::RESET . '/$schema',
		Exceptions\ParseMessageException::class,
		'Provided topic is not valid',
	],
	'child-ctrl-' . Entities\Control::RECONNECT     => [
		'/fb/v1/device-name/$child/child-name/$control/' . Entities\Control::RECONNECT . '/$schema',
		Exceptions\ParseMessageException::class,
		'Provided topic is not valid',
	],
	'child-ctrl-' . Entities\Control::FACTORY_RESET => [
		'/fb/v1/device-name/$child/child-name/$control/' . Entities\Control::FACTORY_RESET . '/$schema',
		Exceptions\ParseMessageException::class,
		'Provided topic is not valid',
	],
	'child-ctrl-unknown'                            => [
		'/fb/v1/device-name/$child/child-name/$control/unknown',
		Exceptions\ParseMessageException::class,
		'Provided topic is not valid',
	],
];
