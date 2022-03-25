<?php declare(strict_types = 1);

use FastyBird\FbMqttConnector\Exceptions;

return [
	'hw-not-valid'       => [
		'/fb/v1/device-name/$hw/not-valid',
		Exceptions\ParseMessageException::class,
		'Provided topic is not valid',
	],
];
