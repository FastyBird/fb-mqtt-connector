<?php declare(strict_types = 1);

use FastyBird\MqttConnectorPlugin\Entities;

return [
	'fw-' . Entities\Firmware::MANUFACTURER       => [
		'/fb/v1/device-name/$fw/' . Entities\Firmware::MANUFACTURER,
		'value-content',
		[
			'client_id'                     => '4f7180ae-6195-460d-aae2-35bfc6124bbc',
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
			'client_id'             => '4f7180ae-6195-460d-aae2-35bfc6124bbc',
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
			'client_id'                => '4f7180ae-6195-460d-aae2-35bfc6124bbc',
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
			'client_id'                     => '4f7180ae-6195-460d-aae2-35bfc6124bbc',
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
			'client_id'             => '4f7180ae-6195-460d-aae2-35bfc6124bbc',
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
			'client_id'                => '4f7180ae-6195-460d-aae2-35bfc6124bbc',
			'device'                   => 'child-name',
			'parent'                   => 'device-name',
			'retained'                 => false,
			Entities\Firmware::VERSION => 'value-content',
		],
	],
];
