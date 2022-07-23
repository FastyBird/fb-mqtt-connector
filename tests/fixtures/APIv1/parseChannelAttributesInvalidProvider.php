<?php declare(strict_types = 1);

use FastyBird\FbMqttConnector\Entities;
use FastyBird\FbMqttConnector\Exceptions;

return [
	'attr-' . Entities\Messages\AttributeEntity::CHANNELS       => [
		'/fb/v1/device-name/$channel/channel-name/$' . Entities\Messages\AttributeEntity::CHANNELS,
		Exceptions\ParseMessageException::class,
		'Provided topic is not valid',
	],
	'attr-other'                                 => [
		'/fb/v1/device-name/$channel/channel-name/$other',
		Exceptions\ParseMessageException::class,
		'Provided topic is not valid',
	],
];
