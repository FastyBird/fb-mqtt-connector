<?php declare(strict_types = 1);

use FastyBird\FbMqttConnector\Entities;

return [
	'fw-' . Entities\Messages\ExtensionAttributeEntity::MANUFACTURER => [
		'/fb/v1/device-name/$fw/' . Entities\Messages\ExtensionAttributeEntity::MANUFACTURER,
		'value-content',
		[
			'device'                                           => 'device-name',
			'retained'                                         => false,
			Entities\Messages\ExtensionAttributeEntity::MANUFACTURER => 'value-content',
		],
	],
	'fw-' . Entities\Messages\ExtensionAttributeEntity::VERSION      => [
		'/fb/v1/device-name/$fw/' . Entities\Messages\ExtensionAttributeEntity::VERSION,
		'value-content',
		[
			'device'                                      => 'device-name',
			'retained'                                    => false,
			Entities\Messages\ExtensionAttributeEntity::VERSION => 'value-content',
		],
	],
];
