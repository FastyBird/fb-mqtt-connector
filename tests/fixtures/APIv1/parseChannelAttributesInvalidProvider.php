<?php declare(strict_types = 1);

use FastyBird\MqttConnectorPlugin\Entities;
use FastyBird\MqttConnectorPlugin\Exceptions;

return [
	'attr-' . Entities\Attribute::CHANNELS       => [
		'/fb/v1/device-name/$channel/channel-name/$' . Entities\Attribute::CHANNELS,
		Exceptions\ParseMessageException::class,
		'Provided topic is not valid',
	],
	'attr-other'                                 => [
		'/fb/v1/device-name/$channel/channel-name/$other',
		Exceptions\ParseMessageException::class,
		'Provided topic is not valid',
	],
	'child-attr-' . Entities\Attribute::CHANNELS => [
		'/fb/v1/device-name/$child/child-name/$channel/channel-name/$' . Entities\Attribute::CHANNELS,
		Exceptions\ParseMessageException::class,
		'Provided topic is not valid',
	],
	'child-attr-other'                           => [
		'/fb/v1/device-name/$child/child-name/$channel/channel-name/$other',
		Exceptions\ParseMessageException::class,
		'Provided topic is not valid',
	],
];
