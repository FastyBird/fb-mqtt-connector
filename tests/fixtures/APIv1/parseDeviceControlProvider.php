<?php declare(strict_types = 1);

use FastyBird\MqttPlugin\Entities;
use Nette\Utils;

$randomDeviceCfgSchemaInput = [
	[
		'type' => 'number',
		'name' => 'Cfg field name',
	],
	[
		'type' => 'text',
		'name' => 'Cfg field name',
	],
	[
		'name' => 'Will be ignored',
	],
	[
		'paramOne' => [
			'type' => 'number',
			'name' => 'Cfg field name',
		],
		'paramTwo' => [
			'type' => 'text',
			'name' => 'Cfg field name',
		],
	],
];

$randomDeviceCfgSchemaOutput = [
	[
		'type'    => 'number',
		'name'    => 'Cfg field name',
		'title'   => null,
		'comment' => null,
		'default' => null,
		'min'     => null,
		'max'     => null,
		'step'    => null,
	],
	[
		'type'    => 'text',
		'name'    => 'Cfg field name',
		'title'   => null,
		'comment' => null,
		'default' => null,
	],
];

$randomDeviceCfg = [
	'some-cfg-attr'  => 10,
	'other-cfg-attr' => 'string',
];

return [
	'schema-ctrl-' . Entities\Control::CONFIG       => [
		'/fb/v1/device-name/$control/' . Entities\Control::CONFIG . '/$schema',
		Utils\Json::encode($randomDeviceCfgSchemaInput),
		[
			'device'   => 'device-name',
			'parent'   => null,
			'retained' => false,
			'control'  => Entities\Control::CONFIG,
			'schema'   => $randomDeviceCfgSchemaOutput,
		],
	],
	'ctrl-' . Entities\Control::CONFIG              => [
		'/fb/v1/device-name/$control/' . Entities\Control::CONFIG,
		Utils\Json::encode($randomDeviceCfg),
		[
			'device'   => 'device-name',
			'parent'   => null,
			'retained' => false,
			'control'  => Entities\Control::CONFIG,
			'value'    => $randomDeviceCfg,
		],
	],
	'child-schema-ctrl-' . Entities\Control::CONFIG => [
		'/fb/v1/device-name/$child/child-name/$control/' . Entities\Control::CONFIG . '/$schema',
		Utils\Json::encode($randomDeviceCfgSchemaInput),
		[
			'device'   => 'child-name',
			'parent'   => 'device-name',
			'retained' => false,
			'control'  => Entities\Control::CONFIG,
			'schema'   => $randomDeviceCfgSchemaOutput,
		],
	],
	'child-ctrl-' . Entities\Control::CONFIG        => [
		'/fb/v1/device-name/$child/child-name/$control/' . Entities\Control::CONFIG,
		Utils\Json::encode($randomDeviceCfg),
		[
			'device'   => 'child-name',
			'parent'   => 'device-name',
			'retained' => false,
			'control'  => Entities\Control::CONFIG,
			'value'    => $randomDeviceCfg,
		],
	],
];
