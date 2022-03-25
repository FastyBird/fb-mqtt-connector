<?php declare(strict_types = 1);

use FastyBird\FbMqttConnector\Entities;

return [
	'attr-' . Entities\Messages\PropertyAttribute::NAME            => [
		'/fb/v1/device-name/$property/some-property/$' . Entities\Messages\PropertyAttribute::NAME,
		'payload',
		[
			'device'                                  => 'device-name',
			'retained'                                => false,
			'property'                                => 'some-property',
			Entities\Messages\PropertyAttribute::NAME => 'payload',
		],
	],
	'attr-' . Entities\Messages\PropertyAttribute::TYPE            => [
		'/fb/v1/device-name/$property/some-property/$' . Entities\Messages\PropertyAttribute::TYPE,
		'typename',
		[
			'device'                                  => 'device-name',
			'retained'                                => false,
			'property'                                => 'some-property',
			Entities\Messages\PropertyAttribute::TYPE => 'typename',
		],
	],
	'attr-' . Entities\Messages\PropertyAttribute::SETTABLE        => [
		'/fb/v1/device-name/$property/some-property/$' . Entities\Messages\PropertyAttribute::SETTABLE,
		'true',
		[
			'device'                                      => 'device-name',
			'retained'                                    => false,
			'property'                                    => 'some-property',
			Entities\Messages\PropertyAttribute::SETTABLE => true,
		],
	],
	'attr-' . Entities\Messages\PropertyAttribute::QUERYABLE       => [
		'/fb/v1/device-name/$property/some-property/$' . Entities\Messages\PropertyAttribute::QUERYABLE,
		'invalid',
		[
			'device'                                       => 'device-name',
			'retained'                                     => false,
			'property'                                     => 'some-property',
			Entities\Messages\PropertyAttribute::QUERYABLE => false,
		],
	],
];
