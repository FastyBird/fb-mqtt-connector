<?php declare(strict_types = 1);

use FastyBird\FbMqttConnector\Entities;

return [
	'attr-' . Entities\Messages\AttributeEntity::NAME             => [
		'/fb/v1/device-name/$channel/channel-name/$' . Entities\Messages\AttributeEntity::NAME,
		'Some content',
		[
			'device'                 => 'device-name',
			'channel'                => 'channel-name',
			'retained'               => false,
			Entities\Messages\AttributeEntity::NAME => 'Some content',
		],
	],
	'attr-' . Entities\Messages\AttributeEntity::PROPERTIES       => [
		'/fb/v1/device-name/$channel/channel-name/$' . Entities\Messages\AttributeEntity::PROPERTIES,
		'prop1,prop2',
		[
			'device'                       => 'device-name',
			'channel'                      => 'channel-name',
			'retained'                     => false,
			Entities\Messages\AttributeEntity::PROPERTIES => ['prop1', 'prop2'],
		],
	],
	'attr-' . Entities\Messages\AttributeEntity::CONTROLS          => [
		'/fb/v1/device-name/$channel/channel-name/$' . Entities\Messages\AttributeEntity::CONTROLS,
		'configure,reset',
		[
			'device'                    => 'device-name',
			'channel'                   => 'channel-name',
			'retained'                  => false,
			Entities\Messages\AttributeEntity::CONTROLS => ['configure', 'reset'],
		],
	],
];
