<?php declare(strict_types = 1);

return [
	'prop-property-name'       => [
		'/fb/v1/device-name/$property/property-name',
		'content',
		[
			'client_id' => '4f7180ae-6195-460d-aae2-35bfc6124bbc',
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

			'client_id' => '4f7180ae-6195-460d-aae2-35bfc6124bbc',
			'device'    => 'child-name',
			'parent'    => 'device-name',
			'retained'  => false,
			'property'  => 'property-name',
			'value'     => 'content',
		],
	],
];
