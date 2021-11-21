<?php declare(strict_types = 1);

use FastyBird\MqttConnectorPlugin\Entities;

return [
	'hw-' . Entities\Hardware::MAC_ADDRESS        => [
		'/fb/v1/device-name/$hw/' . Entities\Hardware::MAC_ADDRESS,
		'00:0a:95:9d:68:16',
		[
			'client_id'                    => '4f7180ae-6195-460d-aae2-35bfc6124bbc',
			'device'                       => 'device-name',
			'parent'                       => null,
			'retained'                     => false,
			Entities\Hardware::MAC_ADDRESS => '000a959d6816',
		],
	],
	'hw-' . Entities\Hardware::MANUFACTURER       => [
		'/fb/v1/device-name/$hw/' . Entities\Hardware::MANUFACTURER,
		'value-content',
		[
			'client_id'                     => '4f7180ae-6195-460d-aae2-35bfc6124bbc',
			'device'                        => 'device-name',
			'parent'                        => null,
			'retained'                      => false,
			Entities\Hardware::MANUFACTURER => 'value-content',
		],
	],
	'hw-' . Entities\Hardware::MODEL              => [
		'/fb/v1/device-name/$hw/' . Entities\Hardware::MODEL,
		'value-content',
		[
			'client_id'              => '4f7180ae-6195-460d-aae2-35bfc6124bbc',
			'device'                 => 'device-name',
			'parent'                 => null,
			'retained'               => false,
			Entities\Hardware::MODEL => 'value-content',
		],
	],
	'hw-' . Entities\Hardware::VERSION            => [
		'/fb/v1/device-name/$hw/' . Entities\Hardware::VERSION,
		'value-content',
		[
			'client_id'                => '4f7180ae-6195-460d-aae2-35bfc6124bbc',
			'device'                   => 'device-name',
			'parent'                   => null,
			'retained'                 => false,
			Entities\Hardware::VERSION => 'value-content',
		],
	],
	'child-hw-' . Entities\Hardware::MAC_ADDRESS  => [
		'/fb/v1/device-name/$child/child-name/$hw/' . Entities\Hardware::MAC_ADDRESS,
		'00:0a:95:9d:68:16',
		[
			'client_id'                    => '4f7180ae-6195-460d-aae2-35bfc6124bbc',
			'device'                       => 'child-name',
			'parent'                       => 'device-name',
			'retained'                     => false,
			Entities\Hardware::MAC_ADDRESS => '000a959d6816',
		],
	],
	'child-hw-' . Entities\Hardware::MANUFACTURER => [
		'/fb/v1/device-name/$child/child-name/$hw/' . Entities\Hardware::MANUFACTURER,
		'value-content',
		[
			'client_id'                     => '4f7180ae-6195-460d-aae2-35bfc6124bbc',
			'device'                        => 'child-name',
			'parent'                        => 'device-name',
			'retained'                      => false,
			Entities\Hardware::MANUFACTURER => 'value-content',
		],
	],
	'child-hw-' . Entities\Hardware::MODEL        => [
		'/fb/v1/device-name/$child/child-name/$hw/' . Entities\Hardware::MODEL,
		'value-content',
		[
			'client_id'              => '4f7180ae-6195-460d-aae2-35bfc6124bbc',
			'device'                 => 'child-name',
			'parent'                 => 'device-name',
			'retained'               => false,
			Entities\Hardware::MODEL => 'value-content',
		],
	],
	'child-hw-' . Entities\Hardware::VERSION      => [
		'/fb/v1/device-name/$child/child-name/$hw/' . Entities\Hardware::VERSION,
		'value-content',
		[
			'client_id'                => '4f7180ae-6195-460d-aae2-35bfc6124bbc',
			'device'                   => 'child-name',
			'parent'                   => 'device-name',
			'retained'                 => false,
			Entities\Hardware::VERSION => 'value-content',
		],
	],
];
