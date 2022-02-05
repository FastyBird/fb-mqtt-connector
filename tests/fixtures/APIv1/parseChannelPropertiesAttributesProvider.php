<?php declare(strict_types = 1);

use FastyBird\FbMqttConnector\Entities;

return [
	'attr-' . Entities\Messages\PropertyAttribute::NAME            => [
		'/fb/v1/device-name/$channel/channel-name/$property/some-property/$' . Entities\Messages\PropertyAttribute::NAME,
		'payload',
		[
			'device'                                  => 'device-name',
			'parent'                                  => null,
			'channel'                                 => 'channel-name',
			'retained'                                => false,
			'property'                                => 'some-property',
			Entities\Messages\PropertyAttribute::NAME => 'payload',
		],
	],
	'attr-' . Entities\Messages\PropertyAttribute::TYPE            => [
		'/fb/v1/device-name/$channel/channel-name/$property/some-property/$' . Entities\Messages\PropertyAttribute::TYPE,
		'typename',
		[
			'device'                                  => 'device-name',
			'parent'                                  => null,
			'channel'                                 => 'channel-name',
			'retained'                                => false,
			'property'                                => 'some-property',
			Entities\Messages\PropertyAttribute::TYPE => 'typename',
		],
	],
	'attr-' . Entities\Messages\PropertyAttribute::SETTABLE        => [
		'/fb/v1/device-name/$channel/channel-name/$property/some-property/$' . Entities\Messages\PropertyAttribute::SETTABLE,
		'true',
		[
			'device'                                      => 'device-name',
			'parent'                                      => null,
			'channel'                                     => 'channel-name',
			'retained'                                    => false,
			'property'                                    => 'some-property',
			Entities\Messages\PropertyAttribute::SETTABLE => true,
		],
	],
	'attr-' . Entities\Messages\PropertyAttribute::QUERYABLE       => [
		'/fb/v1/device-name/$channel/channel-name/$property/some-property/$' . Entities\Messages\PropertyAttribute::QUERYABLE,
		'invalid',
		[
			'device'                                       => 'device-name',
			'parent'                                       => null,
			'channel'                                      => 'channel-name',
			'retained'                                     => false,
			'property'                                     => 'some-property',
			Entities\Messages\PropertyAttribute::QUERYABLE => false,
		],
	],
	'child-attr-' . Entities\Messages\PropertyAttribute::NAME      => [
		'/fb/v1/device-name/$child/child-name/$channel/channel-name/$property/some-property/$' . Entities\Messages\PropertyAttribute::NAME,
		'payload',
		[
			'device'                                  => 'child-name',
			'parent'                                  => 'device-name',
			'channel'                                 => 'channel-name',
			'retained'                                => false,
			'property'                                => 'some-property',
			Entities\Messages\PropertyAttribute::NAME => 'payload',
		],
	],
	'child-attr-' . Entities\Messages\PropertyAttribute::TYPE      => [
		'/fb/v1/device-name/$child/child-name/$channel/channel-name/$property/some-property/$' . Entities\Messages\PropertyAttribute::TYPE,
		'typename',
		[
			'device'                                  => 'child-name',
			'parent'                                  => 'device-name',
			'channel'                                 => 'channel-name',
			'retained'                                => false,
			'property'                                => 'some-property',
			Entities\Messages\PropertyAttribute::TYPE => 'typename',
		],
	],
	'child-attr-' . Entities\Messages\PropertyAttribute::SETTABLE  => [
		'/fb/v1/device-name/$child/child-name/$channel/channel-name/$property/some-property/$' . Entities\Messages\PropertyAttribute::SETTABLE,
		'true',
		[
			'device'                                      => 'child-name',
			'parent'                                      => 'device-name',
			'channel'                                     => 'channel-name',
			'retained'                                    => false,
			'property'                                    => 'some-property',
			Entities\Messages\PropertyAttribute::SETTABLE => true,
		],
	],
	'child-attr-' . Entities\Messages\PropertyAttribute::QUERYABLE => [
		'/fb/v1/device-name/$child/child-name/$channel/channel-name/$property/some-property/$' . Entities\Messages\PropertyAttribute::QUERYABLE,
		'invalid',
		[
			'device'                                       => 'child-name',
			'parent'                                       => 'device-name',
			'channel'                                      => 'channel-name',
			'retained'                                     => false,
			'property'                                     => 'some-property',
			Entities\Messages\PropertyAttribute::QUERYABLE => false,
		],
	],
];
