<?php declare(strict_types = 1);

use FastyBird\FbMqttConnector\Entities;

return [
	'hw-' . Entities\Messages\Hardware::MAC_ADDRESS        => [
		'/fb/v1/device-name/$hw/' . Entities\Messages\Hardware::MAC_ADDRESS,
		'00:0a:95:9d:68:16',
		[
			'device'                                => 'device-name',
			'parent'                                => null,
			'retained'                              => false,
			Entities\Messages\Hardware::MAC_ADDRESS => '000a959d6816',
		],
	],
	'hw-' . Entities\Messages\Hardware::MANUFACTURER       => [
		'/fb/v1/device-name/$hw/' . Entities\Messages\Hardware::MANUFACTURER,
		'value-content',
		[
			'device'                                 => 'device-name',
			'parent'                                 => null,
			'retained'                               => false,
			Entities\Messages\Hardware::MANUFACTURER => 'value-content',
		],
	],
	'hw-' . Entities\Messages\Hardware::MODEL              => [
		'/fb/v1/device-name/$hw/' . Entities\Messages\Hardware::MODEL,
		'value-content',
		[
			'device'                          => 'device-name',
			'parent'                          => null,
			'retained'                        => false,
			Entities\Messages\Hardware::MODEL => 'value-content',
		],
	],
	'hw-' . Entities\Messages\Hardware::VERSION            => [
		'/fb/v1/device-name/$hw/' . Entities\Messages\Hardware::VERSION,
		'value-content',
		[
			'device'                            => 'device-name',
			'parent'                            => null,
			'retained'                          => false,
			Entities\Messages\Hardware::VERSION => 'value-content',
		],
	],
	'child-hw-' . Entities\Messages\Hardware::MAC_ADDRESS  => [
		'/fb/v1/device-name/$child/child-name/$hw/' . Entities\Messages\Hardware::MAC_ADDRESS,
		'00:0a:95:9d:68:16',
		[
			'device'                                => 'child-name',
			'parent'                                => 'device-name',
			'retained'                              => false,
			Entities\Messages\Hardware::MAC_ADDRESS => '000a959d6816',
		],
	],
	'child-hw-' . Entities\Messages\Hardware::MANUFACTURER => [
		'/fb/v1/device-name/$child/child-name/$hw/' . Entities\Messages\Hardware::MANUFACTURER,
		'value-content',
		[
			'device'                                 => 'child-name',
			'parent'                                 => 'device-name',
			'retained'                               => false,
			Entities\Messages\Hardware::MANUFACTURER => 'value-content',
		],
	],
	'child-hw-' . Entities\Messages\Hardware::MODEL        => [
		'/fb/v1/device-name/$child/child-name/$hw/' . Entities\Messages\Hardware::MODEL,
		'value-content',
		[
			'device'                          => 'child-name',
			'parent'                          => 'device-name',
			'retained'                        => false,
			Entities\Messages\Hardware::MODEL => 'value-content',
		],
	],
	'child-hw-' . Entities\Messages\Hardware::VERSION      => [
		'/fb/v1/device-name/$child/child-name/$hw/' . Entities\Messages\Hardware::VERSION,
		'value-content',
		[
			'device'                            => 'child-name',
			'parent'                            => 'device-name',
			'retained'                          => false,
			Entities\Messages\Hardware::VERSION => 'value-content',
		],
	],
];
