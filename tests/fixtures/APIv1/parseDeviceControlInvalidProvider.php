<?php declare(strict_types = 1);

use FastyBird\MqttConnectorPlugin\Entities;
use FastyBird\MqttConnectorPlugin\Exceptions;

return [
	'ctrl-' . Entities\Control::RESET               => [
		'/fb/v1/device-name/$control/' . Entities\Control::RESET . '/$schema',
		Exceptions\InvalidStateException::class,
		'Schema could be set only for "configure" control type',
	],
	'ctrl-' . Entities\Control::RECONNECT           => [
		'/fb/v1/device-name/$control/' . Entities\Control::RECONNECT . '/$schema',
		Exceptions\InvalidStateException::class,
		'Schema could be set only for "configure" control type',
	],
	'ctrl-' . Entities\Control::FACTORY_RESET       => [
		'/fb/v1/device-name/$control/' . Entities\Control::FACTORY_RESET . '/$schema',
		Exceptions\InvalidStateException::class,
		'Schema could be set only for "configure" control type',
	],
	'ctrl-unknown'                                  => [
		'/fb/v1/device-name/$control/unknown',
		Exceptions\ParseMessageException::class,
		'Provided topic is not valid',
	],
	'child-ctrl-' . Entities\Control::RESET         => [
		'/fb/v1/device-name/$child/child-name/$control/' . Entities\Control::RESET . '/$schema',
		Exceptions\InvalidStateException::class,
		'Schema could be set only for "configure" control type',
	],
	'child-ctrl-' . Entities\Control::RECONNECT     => [
		'/fb/v1/device-name/$child/child-name/$control/' . Entities\Control::RECONNECT . '/$schema',
		Exceptions\InvalidStateException::class,
		'Schema could be set only for "configure" control type',
	],
	'child-ctrl-' . Entities\Control::FACTORY_RESET => [
		'/fb/v1/device-name/$child/child-name/$control/' . Entities\Control::FACTORY_RESET . '/$schema',
		Exceptions\InvalidStateException::class,
		'Schema could be set only for "configure" control type',
	],
	'child-ctrl-unknown'                            => [
		'/fb/v1/device-name/$child/child-name/$control/unknown',
		Exceptions\ParseMessageException::class,
		'Provided topic is not valid',
	],
];
