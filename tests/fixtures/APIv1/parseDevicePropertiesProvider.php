<?php declare(strict_types = 1);

return [
	'prop-property-name'       => [
		'/fb/v1/device-name/$property/property-name',
		'content',
		[
			'device'    => 'device-name',
			'parent'    => null,
			'retained'  => false,
			'property'  => 'property-name',
			'value'     => 'content',
		],
	],
	'child-prop-property-name' => [
		'/fb/v1/device-name/$child/child-name/$property/property-name',
		'content',
		[

			'device'    => 'child-name',
			'parent'    => 'device-name',
			'retained'  => false,
			'property'  => 'property-name',
			'value'     => 'content',
		],
	],
];
