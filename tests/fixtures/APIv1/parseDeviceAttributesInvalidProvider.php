<?php declare(strict_types = 1);

use FastyBird\FbMqttConnector\Exceptions;

return [
	'attr-unknown'       => [
		'/fb/v1/device-name/$unknown',
		Exceptions\ParseMessageException::class,
		'Provided topic is not valid',
	],
	'child-attr-unknown' => [
		'/fb/v1/device-name/$child/child-name/$unknown',
		Exceptions\ParseMessageException::class,
		'Provided topic is not valid',
	],
];
