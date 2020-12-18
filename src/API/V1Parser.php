<?php declare(strict_types = 1);

/**
 * V1Parser.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:MqttPlugin!
 * @subpackage     API
 * @since          0.1.0
 *
 * @date           24.02.20
 */

namespace FastyBird\MqttPlugin\API;

use FastyBird\MqttPlugin;
use FastyBird\MqttPlugin\Entities;
use FastyBird\MqttPlugin\Exceptions;
use Nette;
use Nette\Utils;

/**
 * API v1 topic parser
 *
 * @package        FastyBird:MqttPlugin!
 * @subpackage     API
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class V1Parser
{

	use Nette\SmartObject;

	/** @var V1Validator */
	private V1Validator $validator;

	public function __construct(
		V1Validator $validator
	) {
		$this->validator = $validator;
	}

	/**
	 * @param string $topic
	 * @param string $payload
	 * @param bool $retained
	 *
	 * @return Entities\IEntity
	 */
	public function parse(string $topic, string $payload, bool $retained = false): Entities\IEntity
	{
		if (!$this->validator->validate($topic)) {
			throw new Exceptions\ParseMessageException('Provided topic is not valid');
		}

		$isChild = $this->validator->validateChildDevicePart($topic);

		if ($this->validator->validateDeviceAttribute($topic)) {
			$entity = $this->parseDeviceAttribute($topic, $payload, $isChild);
			$entity->setRetained($retained);

			return $entity;
		}

		if ($this->validator->validateDeviceHardwareInfo($topic)) {
			$entity = $this->parseDeviceHardwareInfo($topic, $payload, $isChild);
			$entity->setRetained($retained);

			return $entity;
		}

		if ($this->validator->validateDeviceFirmwareInfo($topic)) {
			$entity = $this->parseDeviceFirmwareInfo($topic, $payload, $isChild);
			$entity->setRetained($retained);

			return $entity;
		}

		if ($this->validator->validateDeviceProperty($topic)) {
			$entity = $this->parseDeviceProperty($topic, $payload, $isChild);
			$entity->setRetained($retained);

			return $entity;
		}

		if ($this->validator->validateDeviceControl($topic)) {
			$entity = $this->parseDeviceControl($topic, $payload, $isChild);
			$entity->setRetained($retained);

			return $entity;
		}

		if ($this->validator->validateChannelPart($topic)) {
			if ($isChild) {
				preg_match(V1Validator::CHILD_DEVICE_CHANNEL_PARTIAL_REGEXP, $topic, $matches);
				[, $parent, $device] = $matches;

			} else {
				preg_match(V1Validator::CHANNEL_PARTIAL_REGEXP, $topic, $matches);
				[, $device] = $matches;
				$parent = null;
			}

			if ($this->validator->validateChannelAttribute($topic)) {
				$entity = $this->parseChannelAttribute($device, $parent, $topic, $payload);
				$entity->setRetained($retained);

				return $entity;
			}

			if ($this->validator->validateChannelProperty($topic)) {
				$entity = $this->parseChannelProperty($device, $parent, $topic, $payload);
				$entity->setRetained($retained);

				return $entity;
			}

			if ($this->validator->validateChannelControl($topic)) {
				$entity = $this->parseChannelControl($device, $parent, $topic, $payload);
				$entity->setRetained($retained);

				return $entity;
			}
		}

		throw new Exceptions\ParseMessageException('Provided topic is not valid');
	}

	/**
	 * @param string $topic
	 * @param string $payload
	 * @param bool $isChild
	 *
	 * @return Entities\DeviceAttribute
	 */
	private function parseDeviceAttribute(
		string $topic,
		string $payload,
		bool $isChild = false
	): Entities\DeviceAttribute {
		if ($isChild) {
			preg_match(V1Validator::DEVICE_CHILD_ATTRIBUTE_REGEXP, $topic, $matches);
			[, $parent, $device, $attribute] = $matches;

		} else {
			preg_match(V1Validator::DEVICE_ATTRIBUTE_REGEXP, $topic, $matches);
			[, $device, $attribute] = $matches;
			$parent = null;
		}

		return new Entities\DeviceAttribute(
			$device,
			$attribute,
			$this->parseAttributePayload($payload, $attribute),
			$parent
		);
	}

	/**
	 * @param string $payload
	 * @param string $attribute
	 *
	 * @return string|string[]
	 */
	private function parseAttributePayload(
		string $payload,
		string $attribute
	) {
		if ($attribute === Entities\Attribute::NAME) {
			$payload = $this->cleanName($payload);

		} else {
			$payload = $this->cleanPayload($payload);

			if (
				$attribute === Entities\Attribute::PROPERTIES
				|| $attribute === Entities\Attribute::CHANNELS
				|| $attribute === Entities\Attribute::EXTENSIONS
				|| $attribute === Entities\Attribute::CONTROL
			) {
				$payload = array_filter(
					array_map('trim', explode(',', strtolower($payload))),
					function ($item): bool {
						return $item !== '';
					}
				);

				$payload = array_values($payload);
				$payload = array_unique($payload);
			}
		}

		return $payload;
	}

	/**
	 * @param string $payload
	 *
	 * @return string
	 */
	private function cleanName(string $payload): string
	{
		$cleaned = preg_replace('/[^A-Za-z0-9.,_ -]/', '', $payload);

		return is_string($cleaned) ? $cleaned : '';
	}

	/**
	 * @param string $payload
	 *
	 * @return string
	 */
	private function cleanPayload(string $payload): string
	{
		// Remove all characters except A-Z, a-z, 0-9, dots, commas, [, ], hyphens and spaces
		// Note that the hyphen must go last not to be confused with a range (A-Z)
		// and the dot, being special, is escaped with \
		$payload = preg_replace('/[^A-Za-z0-9.:, -_°%µ³\/\"]/', '', $payload);

		if (!is_string($payload)) {
			return '';
		}

		return $payload;
	}

	/**
	 * @param string $topic
	 * @param string $payload
	 * @param bool $isChild
	 *
	 * @return Entities\Hardware
	 */
	private function parseDeviceHardwareInfo(
		string $topic,
		string $payload,
		bool $isChild = false
	): Entities\Hardware {
		if ($isChild) {
			preg_match(V1Validator::DEVICE_CHILD_HW_INFO_REGEXP, $topic, $matches);
			[, $parent, $device, $hardware] = $matches;

		} else {
			preg_match(V1Validator::DEVICE_HW_INFO_REGEXP, $topic, $matches);
			[, $device, $hardware] = $matches;
			$parent = null;
		}

		return new Entities\Hardware($device, $hardware, $this->cleanName(strtolower($payload)), $parent);
	}

	/**
	 * @param string $topic
	 * @param string $payload
	 * @param bool $isChild
	 *
	 * @return Entities\Firmware
	 */
	private function parseDeviceFirmwareInfo(
		string $topic,
		string $payload,
		bool $isChild = false
	): Entities\Firmware {
		if ($isChild) {
			preg_match(V1Validator::DEVICE_CHILD_FW_INFO_REGEXP, $topic, $matches);
			[, $parent, $device, $firmware] = $matches;

		} else {
			preg_match(V1Validator::DEVICE_FW_INFO_REGEXP, $topic, $matches);
			[, $device, $firmware] = $matches;
			$parent = null;
		}

		return new Entities\Firmware($device, $firmware, $this->cleanName(strtolower($payload)), $parent);
	}

	/**
	 * @param string $topic
	 * @param string $payload
	 * @param bool $isChild
	 *
	 * @return Entities\DeviceProperty
	 */
	private function parseDeviceProperty(
		string $topic,
		string $payload,
		bool $isChild = false
	): Entities\DeviceProperty {
		if ($isChild) {
			preg_match(V1Validator::DEVICE_CHILD_PROPERTY_REGEXP, $topic, $matches);
			[, $parent, $device, $property, , , $attribute] = $matches + [null, null, null, null, null, null, null];

		} else {
			preg_match(V1Validator::DEVICE_PROPERTY_REGEXP, $topic, $matches);
			[, $device, $property, , , $attribute] = $matches + [null, null, null, null, null, null];
			$parent = null;
		}

		$entity = new Entities\DeviceProperty($device, $property, $parent);

		if ($attribute !== null) {
			$attribute = $this->parsePropertyAttribute($payload, $attribute);

			$entity->addAttribute($attribute);

		} else {
			$entity->setValue($payload);
		}

		return $entity;
	}

	/**
	 * @param string $payload
	 * @param string $attribute
	 *
	 * @return Entities\PropertyAttribute
	 */
	private function parsePropertyAttribute(
		string $payload,
		string $attribute
	): Entities\PropertyAttribute {
		if (!in_array($attribute, Entities\PropertyAttribute::ALLOWED_ATTRIBUTES, true)) {
			throw new Exceptions\ParseMessageException('Provided topic is not valid');
		}

		$payload = $this->cleanPayload($payload);

		if (
			$attribute === Entities\PropertyAttribute::SETTABLE
			|| $attribute === Entities\PropertyAttribute::QUERYABLE
		) {
			$payload = $payload === MqttPlugin\Constants::PAYLOAD_BOOL_TRUE_VALUE ? MqttPlugin\Constants::PAYLOAD_BOOL_TRUE_VALUE : MqttPlugin\Constants::PAYLOAD_BOOL_FALSE_VALUE;

		} elseif ($attribute === Entities\PropertyAttribute::NAME) {
			$payload = $this->cleanName($payload);

		} elseif ($attribute === Entities\PropertyAttribute::DATATYPE) {
			if (!in_array($payload, Entities\PropertyAttribute::DATATYPE_ALLOWED_PAYLOADS, true)) {
				throw new Exceptions\ParseMessageException('Provided payload is not valid');
			}

		} elseif ($attribute === Entities\PropertyAttribute::FORMAT) {
			if (Utils\Strings::contains($payload, ':')) {
				[$start, $end] = explode(':', $payload) + [null, null];

				$start = $start === '' ? null : $start;
				$end = $end === '' ? null : $end;

				if ($start !== null && is_numeric($start) === false) {
					throw new Exceptions\ParseMessageException('Provided payload is not valid');
				}

				if ($end !== null && is_numeric($end) === false) {
					throw new Exceptions\ParseMessageException('Provided payload is not valid');
				}

				if ($start !== null) {
					$start = Utils\Strings::contains($start, '.') ? (float) $start : (int) $start;
				}

				if ($end !== null) {
					$end = Utils\Strings::contains($end, '.') ? (float) $end : (int) $end;
				}

				if ($start !== null && $end !== null && $start > $end) {
					throw new Exceptions\ParseMessageException('Provided payload is not valid');
				}

				$payload = (string) $start . ':' . (string) $end;

			} elseif (Utils\Strings::contains($payload, ',')) {
				$payload = array_filter(
					array_map('trim', explode(',', strtolower($payload))),
					function ($item): bool {
						return $item !== '';
					}
				);

				$payload = array_values($payload);
				$payload = array_unique($payload);

				$payload = implode(',', $payload);

			} elseif ($payload === MqttPlugin\Constants::VALUE_NOT_SET || $payload === '') {
				$payload = null;

			} elseif (!in_array($payload, Entities\PropertyAttribute::FORMAT_ALLOWED_PAYLOADS, true)) {
				throw new Exceptions\ParseMessageException('Provided payload is not valid');
			}

		} else {
			$payload = $payload === MqttPlugin\Constants::VALUE_NOT_SET || $payload === '' ? null : $payload;
		}

		return new Entities\PropertyAttribute($attribute, $payload);
	}

	/**
	 * @param string $topic
	 * @param string $payload
	 * @param bool $isChild
	 *
	 * @return Entities\DeviceControl
	 */
	private function parseDeviceControl(
		string $topic,
		string $payload,
		bool $isChild = false
	): Entities\DeviceControl {
		if ($isChild) {
			preg_match(V1Validator::DEVICE_CHILD_CONTROL_REGEXP, $topic, $matches);
			[, $parent, $device, $property, , , $attribute] = $matches + [null, null, null, null, null, null, null];

		} else {
			preg_match(V1Validator::DEVICE_CONTROL_REGEXP, $topic, $matches);
			[, $device, $property, , , $attribute] = $matches + [null, null, null, null, null, null];
			$parent = null;
		}

		$control = new Entities\DeviceControl($device, $property, $parent);

		if ($attribute === null) {
			$control->setValue($payload);

			return $control;

		} elseif ($attribute === 'schema') {
			$control->setSchema($this->parseControlSchema($payload, $property, $attribute));
		}

		return $control;
	}

	/**
	 * @param string $payload
	 * @param string $control
	 * @param string $parameter
	 *
	 * @return mixed[]
	 */
	private function parseControlSchema(
		string $payload,
		string $control,
		string $parameter
	): array {
		if ($control === Entities\Control::CONFIG) {
			try {
				$payload = Utils\Json::decode($payload, Utils\Json::FORCE_ARRAY);

			} catch (Utils\JsonException $ex) {
				throw new Exceptions\ParseMessageException('Control payload is not valid JSON value');
			}

			if ($parameter === 'schema') {
				$schema = [];

				/** @var Utils\ArrayHash $row */
				foreach (Utils\ArrayHash::from($payload) as $row) {
					if (!$row->offsetExists('type') || !$row->offsetExists('name')) {
						continue;
					}

					$formattedRow = Utils\ArrayHash::from([
						'type'    => $row->offsetGet('type'),
						'name'    => $row->offsetGet('name'),
						'title'   => null,
						'comment' => null,
						'default' => null,
					]);

					if ($row->offsetExists('title') && $row->offsetGet('title') !== '') {
						$formattedRow->offsetSet('title', $row->offsetGet('title'));
					}

					if ($row->offsetExists('comment') && $row->offsetGet('comment') !== '') {
						$formattedRow->offsetSet('comment', $row->offsetGet('comment'));
					}

					switch ($row->offsetGet('type')) {
						case Entities\Control::DATA_TYPE_NUMBER:
							foreach (['min', 'max', 'step', 'default'] as $field) {
								if ($row->offsetExists($field)) {
									$formattedRow->offsetSet($field, (float) $row->offsetGet($field));

								} else {
									$formattedRow->offsetSet($field, null);
								}
							}
							break;

						case Entities\Control::DATA_TYPE_TEXT:
							if ($row->offsetExists('default')) {
								$formattedRow->offsetSet('default', (string) $row->offsetGet('default'));
							}
							break;

						case Entities\Control::DATA_TYPE_BOOLEAN:
							if ($row->offsetExists('default')) {
								$formattedRow->offsetSet('default', (bool) $row->offsetGet('default'));
							}
							break;

						case Entities\Control::DATA_TYPE_SELECT:
							if (
								$row->offsetExists('values')
								&& $row->offsetGet('values') instanceof Utils\ArrayHash
							) {
								$formattedRow->offsetSet('values', $row->offsetGet('values'));

							} else {
								$formattedRow->offsetSet('values', []);
							}

							if ($row->offsetExists('default')) {
								$formattedRow->offsetSet('default', (string) $row->offsetGet('default'));
							}
							break;
					}

					$schema[] = (array) $formattedRow;
				}

				return $schema;
			}
		}

		throw new Exceptions\ParseMessageException('Provided topic is not valid');
	}

	/**
	 * @param string $device
	 * @param string|null $parent
	 * @param string $topic
	 * @param string $payload
	 *
	 * @return Entities\ChannelAttribute
	 */
	private function parseChannelAttribute(
		string $device,
		?string $parent,
		string $topic,
		string $payload
	): Entities\ChannelAttribute {
		preg_match(V1Validator::CHANNEL_ATTRIBUTE_REGEXP, $topic, $matches);
		[, , $channel, $attribute] = $matches;

		return new Entities\ChannelAttribute(
			$device,
			$channel,
			$attribute,
			$this->parseAttributePayload($payload, $attribute),
			$parent
		);
	}

	/**
	 * @param string $device
	 * @param string|null $parent
	 * @param string $topic
	 * @param string $payload
	 *
	 * @return Entities\ChannelProperty
	 */
	private function parseChannelProperty(
		string $device,
		?string $parent,
		string $topic,
		string $payload
	): Entities\ChannelProperty {
		preg_match(V1Validator::CHANNEL_PROPERTY_REGEXP, $topic, $matches);
		[, , $channel, $property, , , $attribute] = $matches + [null, null, null, null, null, null, null];

		$entity = new Entities\ChannelProperty($device, $channel, $property, $parent);

		if ($attribute !== null) {
			$attribute = $this->parsePropertyAttribute($payload, $attribute);

			$entity->addAttribute($attribute);

		} else {
			$entity->setValue($payload);
		}

		return $entity;
	}

	/**
	 * @param string $device
	 * @param string|null $parent
	 * @param string $topic
	 * @param string $payload
	 *
	 * @return Entities\ChannelControl
	 */
	private function parseChannelControl(
		string $device,
		?string $parent,
		string $topic,
		string $payload
	): Entities\ChannelControl {
		preg_match(V1Validator::CHANNEL_CONTROL_REGEXP, $topic, $matches);
		[, , $channel, $property, , , $attribute] = $matches + [null, null, null, null, null, null, null];

		$control = new Entities\ChannelControl($device, $channel, $property, $parent);

		if ($attribute === null) {
			$control->setValue($payload);

			return $control;

		} elseif ($attribute === 'schema') {
			$control->setSchema($this->parseControlSchema($payload, $property, $attribute));
		}

		return $control;
	}

}
