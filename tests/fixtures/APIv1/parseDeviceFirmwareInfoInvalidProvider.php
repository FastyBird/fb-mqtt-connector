<?php declare(strict_types = 1);

use FastyBird\FbMqttConnector\Exceptions;

return [
	'fw-not-valid'       => [
		'/fb/v1/device-name/$fw/not-valid',
		Exceptions\ParseMessageException::class,
		'Provided topic is not valid',
	],
	'child-fw-not-valid' => [
		'/fb/v1/device-name/$child/child-name/$fw/not-valid',
		Exceptions\ParseMessageException::class,
		'Provided topic is not valid',
	],
];
