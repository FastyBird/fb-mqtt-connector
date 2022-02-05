<?php declare(strict_types = 1);

use FastyBird\FbMqttConnector\Entities;

return [
	'fw-' . Entities\Messages\Firmware::MANUFACTURER       => [
		'/fb/v1/device-name/$fw/' . Entities\Messages\Firmware::MANUFACTURER,
		'value-content',
		[
			'device'                                 => 'device-name',
			'parent'                                 => null,
			'retained'                               => false,
			Entities\Messages\Firmware::MANUFACTURER => 'value-content',
		],
	],
	'fw-' . Entities\Messages\Firmware::VERSION            => [
		'/fb/v1/device-name/$fw/' . Entities\Messages\Firmware::VERSION,
		'value-content',
		[
			'device'                            => 'device-name',
			'parent'                            => null,
			'retained'                          => false,
			Entities\Messages\Firmware::VERSION => 'value-content',
		],
	],
	'child-fw-' . Entities\Messages\Firmware::MANUFACTURER => [
		'/fb/v1/device-name/$child/child-name/$fw/' . Entities\Messages\Firmware::MANUFACTURER,
		'value-content',
		[
			'device'                                 => 'child-name',
			'parent'                                 => 'device-name',
			'retained'                               => false,
			Entities\Messages\Firmware::MANUFACTURER => 'value-content',
		],
	],
	'child-fw-' . Entities\Messages\Firmware::VERSION      => [
		'/fb/v1/device-name/$child/child-name/$fw/' . Entities\Messages\Firmware::VERSION,
		'value-content',
		[
			'device'                            => 'child-name',
			'parent'                            => 'device-name',
			'retained'                          => false,
			Entities\Messages\Firmware::VERSION => 'value-content',
		],
	],
];
