<?php declare(strict_types = 1);

use FastyBird\MqttConnectorPlugin\Entities;

return [
	'attr-' . Entities\Attribute::NAME             => [
		'/fb/v1/device-name/$channel/channel-name/$' . Entities\Attribute::NAME,
		'Some content',
		[
			'client_id'              => '4f7180ae-6195-460d-aae2-35bfc6124bbc',
			'device'                 => 'device-name',
			'parent'                 => null,
			'channel'                => 'channel-name',
			'retained'               => false,
			Entities\Attribute::NAME => 'Some content',
		],
	],
	'attr-' . Entities\Attribute::PROPERTIES       => [
		'/fb/v1/device-name/$channel/channel-name/$' . Entities\Attribute::PROPERTIES,
		'prop1,prop2',
		[
			'client_id'                    => '4f7180ae-6195-460d-aae2-35bfc6124bbc',
			'device'                       => 'device-name',
			'parent'                       => null,
			'channel'                      => 'channel-name',
			'retained'                     => false,
			Entities\Attribute::PROPERTIES => ['prop1', 'prop2'],
		],
	],
	'attr-' . Entities\Attribute::CONTROL          => [
		'/fb/v1/device-name/$channel/channel-name/$' . Entities\Attribute::CONTROL,
		'configure,reset',
		[
			'client_id'                 => '4f7180ae-6195-460d-aae2-35bfc6124bbc',
			'device'                    => 'device-name',
			'parent'                    => null,
			'channel'                   => 'channel-name',
			'retained'                  => false,
			Entities\Attribute::CONTROL => ['configure', 'reset'],
		],
	],
	'child-attr-' . Entities\Attribute::NAME       => [
		'/fb/v1/device-name/$child/child-name/$channel/channel-name/$' . Entities\Attribute::NAME,
		'Some content',
		[
			'client_id'              => '4f7180ae-6195-460d-aae2-35bfc6124bbc',
			'device'                 => 'child-name',
			'parent'                 => 'device-name',
			'channel'                => 'channel-name',
			'retained'               => false,
			Entities\Attribute::NAME => 'Some content',
		],
	],
	'child-attr-' . Entities\Attribute::PROPERTIES => [
		'/fb/v1/device-name/$child/child-name/$channel/channel-name/$' . Entities\Attribute::PROPERTIES,
		'prop1,prop2',
		[
			'client_id'                    => '4f7180ae-6195-460d-aae2-35bfc6124bbc',
			'device'                       => 'child-name',
			'parent'                       => 'device-name',
			'channel'                      => 'channel-name',
			'retained'                     => false,
			Entities\Attribute::PROPERTIES => ['prop1', 'prop2'],
		],
	],
	'child-attr-' . Entities\Attribute::CONTROL    => [
		'/fb/v1/device-name/$child/child-name/$channel/channel-name/$' . Entities\Attribute::CONTROL,
		'configure,reset',
		[
			'client_id'                 => '4f7180ae-6195-460d-aae2-35bfc6124bbc',
			'device'                    => 'child-name',
			'parent'                    => 'device-name',
			'channel'                   => 'channel-name',
			'retained'                  => false,
			Entities\Attribute::CONTROL => ['configure', 'reset'],
		],
	],
];
