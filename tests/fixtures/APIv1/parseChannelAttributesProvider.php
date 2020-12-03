<?php declare(strict_types = 1);

use FastyBird\MqttPlugin\Entities;

return [
	'attr-' . Entities\Attribute::NAME             => [
		'/fb/v1/device-name/$channel/channel-name/$' . Entities\Attribute::NAME,
		'Some content',
		[
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
			'device'                    => 'child-name',
			'parent'                    => 'device-name',
			'channel'                   => 'channel-name',
			'retained'                  => false,
			Entities\Attribute::CONTROL => ['configure', 'reset'],
		],
	],
];
