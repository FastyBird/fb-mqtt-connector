<?php declare(strict_types = 1);

use FastyBird\MqttConnectorPlugin\Entities;
use Nette\Utils;

$randomDeviceCfgSchemaInput = [
	[
		'type'       => 'number',
		'identifier' => 'schema-row-1',
		'name'       => 'Cfg field name',
	],
	[
		'type'       => 'text',
		'identifier' => 'schema-row-1',
		'name'       => 'Cfg field name',
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
		'type'       => 'number',
		'identifier' => 'schema-row-1',
		'name'       => 'Cfg field name',
		'data_type'  => 'float',
		'title'      => null,
		'comment'    => null,
		'default'    => null,
		'min'        => null,
		'max'        => null,
		'step'       => null,
	],
	[
		'type'       => 'text',
		'identifier' => 'schema-row-1',
		'name'       => 'Cfg field name',
		'data_type'  => 'string',
		'title'      => null,
		'comment'    => null,
		'default'    => null,
	],
];

$randomDeviceCfg = [
	'some-cfg-attr'  => 10,
	'other-cfg-attr' => 'string',
];

return [
	'schema-ctrl-' . Entities\Control::CONFIG       => [
		'/fb/v1/device-name/$channel/channel-name/$control/' . Entities\Control::CONFIG . '/$schema',
		Utils\Json::encode($randomDeviceCfgSchemaInput),
		[
			'client_id' => '4f7180ae-6195-460d-aae2-35bfc6124bbc',
			'device'    => 'device-name',
			'parent'    => null,
			'channel'   => 'channel-name',
			'retained'  => false,
			'control'   => Entities\Control::CONFIG,
			'schema'    => $randomDeviceCfgSchemaOutput,
		],
	],
	'ctrl-' . Entities\Control::CONFIG              => [
		'/fb/v1/device-name/$channel/channel-name/$control/' . Entities\Control::CONFIG,
		Utils\Json::encode($randomDeviceCfg),
		[
			'client_id' => '4f7180ae-6195-460d-aae2-35bfc6124bbc',
			'device'    => 'device-name',
			'parent'    => null,
			'channel'   => 'channel-name',
			'retained'  => false,
			'control'   => Entities\Control::CONFIG,
			'value'     => $randomDeviceCfg,
		],
	],
	'child-schema-ctrl-' . Entities\Control::CONFIG => [
		'/fb/v1/device-name/$child/child-name/$channel/channel-name/$control/' . Entities\Control::CONFIG . '/$schema',
		Utils\Json::encode($randomDeviceCfgSchemaInput),
		[
			'client_id' => '4f7180ae-6195-460d-aae2-35bfc6124bbc',
			'device'    => 'child-name',
			'parent'    => 'device-name',
			'channel'   => 'channel-name',
			'retained'  => false,
			'control'   => Entities\Control::CONFIG,
			'schema'    => $randomDeviceCfgSchemaOutput,
		],
	],
	'child-ctrl-' . Entities\Control::CONFIG        => [
		'/fb/v1/device-name/$child/child-name/$channel/channel-name/$control/' . Entities\Control::CONFIG,
		Utils\Json::encode($randomDeviceCfg),
		[
			'client_id' => '4f7180ae-6195-460d-aae2-35bfc6124bbc',
			'device'    => 'child-name',
			'parent'    => 'device-name',
			'channel'   => 'channel-name',
			'retained'  => false,
			'control'   => Entities\Control::CONFIG,
			'value'     => $randomDeviceCfg,
		],
	],
];
