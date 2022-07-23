<?php declare(strict_types = 1);

use FastyBird\FbMqttConnector\Entities;

return [
	'attr-' . Entities\Messages\AttributeEntity::NAME             => [
		'/fb/v1/device-name/$' . Entities\Messages\AttributeEntity::NAME,
		'Some content',
		[
			'device'                          => 'device-name',
			'retained'                        => false,
			Entities\Messages\AttributeEntity::NAME => 'Some content',
		],
	],
	'attr-' . Entities\Messages\AttributeEntity::PROPERTIES       => [
		'/fb/v1/device-name/$' . Entities\Messages\AttributeEntity::PROPERTIES,
		'prop1,prop2',
		[
			'device'                                => 'device-name',
			'retained'                              => false,
			Entities\Messages\AttributeEntity::PROPERTIES => ['prop1', 'prop2'],
		],
	],
	'attr-' . Entities\Messages\AttributeEntity::CHANNELS         => [
		'/fb/v1/device-name/$' . Entities\Messages\AttributeEntity::CHANNELS,
		'channel-one,channel-two',
		[
			'device'                              => 'device-name',
			'retained'                            => false,
			Entities\Messages\AttributeEntity::CHANNELS => ['channel-one', 'channel-two'],
		],
	],
	'attr-' . Entities\Messages\AttributeEntity::CONTROLS          => [
		'/fb/v1/device-name/$' . Entities\Messages\AttributeEntity::CONTROLS,
		'configure,reset',
		[
			'device'                             => 'device-name',
			'retained'                           => false,
			Entities\Messages\AttributeEntity::CONTROLS => ['configure', 'reset'],
		],
	],
];
