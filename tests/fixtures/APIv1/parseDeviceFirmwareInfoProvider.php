<?php declare(strict_types = 1);

use FastyBird\MqttConnectorPlugin\Entities;

return [
	'fw-' . Entities\Firmware::MANUFACTURER       => [
		'/fb/v1/device-name/$fw/' . Entities\Firmware::MANUFACTURER,
		'value-content',
		[
			'device'                        => 'device-name',
			'parent'                        => null,
			'retained'                      => false,
			Entities\Firmware::MANUFACTURER => 'value-content',
		],
	],
	'fw-' . Entities\Firmware::NAME               => [
		'/fb/v1/device-name/$fw/' . Entities\Firmware::NAME,
		'value-content',
		[
			'device'                => 'device-name',
			'parent'                => null,
			'retained'              => false,
			Entities\Firmware::NAME => 'value-content',
		],
	],
	'fw-' . Entities\Firmware::VERSION            => [
		'/fb/v1/device-name/$fw/' . Entities\Firmware::VERSION,
		'value-content',
		[
			'device'                   => 'device-name',
			'parent'                   => null,
			'retained'                 => false,
			Entities\Firmware::VERSION => 'value-content',
		],
	],
	'child-fw-' . Entities\Firmware::MANUFACTURER => [
		'/fb/v1/device-name/$child/child-name/$fw/' . Entities\Firmware::MANUFACTURER,
		'value-content',
		[
			'device'                        => 'child-name',
			'parent'                        => 'device-name',
			'retained'                      => false,
			Entities\Firmware::MANUFACTURER => 'value-content',
		],
	],
	'child-fw-' . Entities\Firmware::NAME         => [
		'/fb/v1/device-name/$child/child-name/$fw/' . Entities\Firmware::NAME,
		'value-content',
		[
			'device'                => 'child-name',
			'parent'                => 'device-name',
			'retained'              => false,
			Entities\Firmware::NAME => 'value-content',
		],
	],
	'child-fw-' . Entities\Firmware::VERSION      => [
		'/fb/v1/device-name/$child/child-name/$fw/' . Entities\Firmware::VERSION,
		'value-content',
		[
			'device'                   => 'child-name',
			'parent'                   => 'device-name',
			'retained'                 => false,
			Entities\Firmware::VERSION => 'value-content',
		],
	],
];
