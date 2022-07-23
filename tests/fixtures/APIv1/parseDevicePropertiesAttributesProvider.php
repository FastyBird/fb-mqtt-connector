<?php declare(strict_types = 1);

use FastyBird\FbMqttConnector\Entities;

return [
	'attr-' . Entities\Messages\PropertyAttributeEntity::NAME            => [
		'/fb/v1/device-name/$property/some-property/$' . Entities\Messages\PropertyAttributeEntity::NAME,
		'payload',
		[
			'device'                                  => 'device-name',
			'retained'                                => false,
			'property'                                => 'some-property',
			Entities\Messages\PropertyAttributeEntity::NAME => 'payload',
		],
	],
	'attr-' . Entities\Messages\PropertyAttributeEntity::SETTABLE        => [
		'/fb/v1/device-name/$property/some-property/$' . Entities\Messages\PropertyAttributeEntity::SETTABLE,
		'true',
		[
			'device'                                      => 'device-name',
			'retained'                                    => false,
			'property'                                    => 'some-property',
			Entities\Messages\PropertyAttributeEntity::SETTABLE => true,
		],
	],
	'attr-' . Entities\Messages\PropertyAttributeEntity::QUERYABLE       => [
		'/fb/v1/device-name/$property/some-property/$' . Entities\Messages\PropertyAttributeEntity::QUERYABLE,
		'invalid',
		[
			'device'                                       => 'device-name',
			'retained'                                     => false,
			'property'                                     => 'some-property',
			Entities\Messages\PropertyAttributeEntity::QUERYABLE => false,
		],
	],
];
