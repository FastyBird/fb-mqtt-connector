<?php declare(strict_types = 1);

use FastyBird\MqttConnectorPlugin\Entities;

return [
	'attr-' . Entities\PropertyAttribute::NAME            => [
		'/fb/v1/device-name/$channel/channel-name/$property/some-property/$' . Entities\PropertyAttribute::NAME,
		'payload',
		[
			'client_id'                      => '4f7180ae-6195-460d-aae2-35bfc6124bbc',
			'device'                         => 'device-name',
			'parent'                         => null,
			'channel'                        => 'channel-name',
			'retained'                       => false,
			'property'                       => 'some-property',
			Entities\PropertyAttribute::NAME => 'payload',
		],
	],
	'attr-' . Entities\PropertyAttribute::TYPE            => [
		'/fb/v1/device-name/$channel/channel-name/$property/some-property/$' . Entities\PropertyAttribute::TYPE,
		'typename',
		[
			'client_id'                      => '4f7180ae-6195-460d-aae2-35bfc6124bbc',
			'device'                         => 'device-name',
			'parent'                         => null,
			'channel'                        => 'channel-name',
			'retained'                       => false,
			'property'                       => 'some-property',
			Entities\PropertyAttribute::TYPE => 'typename',
		],
	],
	'attr-' . Entities\PropertyAttribute::SETTABLE        => [
		'/fb/v1/device-name/$channel/channel-name/$property/some-property/$' . Entities\PropertyAttribute::SETTABLE,
		'true',
		[
			'client_id'                          => '4f7180ae-6195-460d-aae2-35bfc6124bbc',
			'device'                             => 'device-name',
			'parent'                             => null,
			'channel'                            => 'channel-name',
			'retained'                           => false,
			'property'                           => 'some-property',
			Entities\PropertyAttribute::SETTABLE => true,
		],
	],
	'attr-' . Entities\PropertyAttribute::QUERYABLE       => [
		'/fb/v1/device-name/$channel/channel-name/$property/some-property/$' . Entities\PropertyAttribute::QUERYABLE,
		'invalid',
		[
			'client_id'                           => '4f7180ae-6195-460d-aae2-35bfc6124bbc',
			'device'                              => 'device-name',
			'parent'                              => null,
			'channel'                             => 'channel-name',
			'retained'                            => false,
			'property'                            => 'some-property',
			Entities\PropertyAttribute::QUERYABLE => false,
		],
	],
	'child-attr-' . Entities\PropertyAttribute::NAME      => [
		'/fb/v1/device-name/$child/child-name/$channel/channel-name/$property/some-property/$' . Entities\PropertyAttribute::NAME,
		'payload',
		[
			'client_id'                      => '4f7180ae-6195-460d-aae2-35bfc6124bbc',
			'device'                         => 'child-name',
			'parent'                         => 'device-name',
			'channel'                        => 'channel-name',
			'retained'                       => false,
			'property'                       => 'some-property',
			Entities\PropertyAttribute::NAME => 'payload',
		],
	],
	'child-attr-' . Entities\PropertyAttribute::TYPE      => [
		'/fb/v1/device-name/$child/child-name/$channel/channel-name/$property/some-property/$' . Entities\PropertyAttribute::TYPE,
		'typename',
		[
			'client_id'                      => '4f7180ae-6195-460d-aae2-35bfc6124bbc',
			'device'                         => 'child-name',
			'parent'                         => 'device-name',
			'channel'                        => 'channel-name',
			'retained'                       => false,
			'property'                       => 'some-property',
			Entities\PropertyAttribute::TYPE => 'typename',
		],
	],
	'child-attr-' . Entities\PropertyAttribute::SETTABLE  => [
		'/fb/v1/device-name/$child/child-name/$channel/channel-name/$property/some-property/$' . Entities\PropertyAttribute::SETTABLE,
		'true',
		[
			'client_id'                          => '4f7180ae-6195-460d-aae2-35bfc6124bbc',
			'device'                             => 'child-name',
			'parent'                             => 'device-name',
			'channel'                            => 'channel-name',
			'retained'                           => false,
			'property'                           => 'some-property',
			Entities\PropertyAttribute::SETTABLE => true,
		],
	],
	'child-attr-' . Entities\PropertyAttribute::QUERYABLE => [
		'/fb/v1/device-name/$child/child-name/$channel/channel-name/$property/some-property/$' . Entities\PropertyAttribute::QUERYABLE,
		'invalid',
		[
			'client_id'                           => '4f7180ae-6195-460d-aae2-35bfc6124bbc',
			'device'                              => 'child-name',
			'parent'                              => 'device-name',
			'channel'                             => 'channel-name',
			'retained'                            => false,
			'property'                            => 'some-property',
			Entities\PropertyAttribute::QUERYABLE => false,
		],
	],
];
