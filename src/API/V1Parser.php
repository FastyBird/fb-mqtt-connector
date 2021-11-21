<?php declare(strict_types = 1);

/**
 * V1Parser.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:MqttConnectorPlugin!
 * @subpackage     API
 * @since          0.1.0
 *
 * @date           24.02.20
 */

namespace FastyBird\MqttConnectorPlugin\API;

use FastyBird\MqttConnectorPlugin\Entities;
use FastyBird\MqttConnectorPlugin\Exceptions;
use FastyBird\MqttConnectorPlugin\Helpers;
use Nette;
use Ramsey\Uuid;

/**
 * API v1 topic parser
 *
 * @package        FastyBird:MqttConnectorPlugin!
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
	 * @param Uuid\UuidInterface $clientId
	 * @param string $topic
	 * @param string $payload
	 * @param bool $retained
	 *
	 * @return Entities\IEntity
	 */
	public function parse(
		Uuid\UuidInterface $clientId,
		string $topic,
		string $payload,
		bool $retained = false
	): Entities\IEntity {
		if (!$this->validator->validate($topic)) {
			throw new Exceptions\ParseMessageException('Provided topic is not valid');
		}

		$isChild = $this->validator->validateChildDevicePart($topic);

		if ($this->validator->validateDeviceAttribute($topic)) {
			$entity = $this->parseDeviceAttribute($clientId, $topic, $payload, $isChild);
			$entity->setRetained($retained);

			return $entity;
		}

		if ($this->validator->validateDeviceHardwareInfo($topic)) {
			$entity = $this->parseDeviceHardwareInfo($clientId, $topic, $payload, $isChild);
			$entity->setRetained($retained);

			return $entity;
		}

		if ($this->validator->validateDeviceFirmwareInfo($topic)) {
			$entity = $this->parseDeviceFirmwareInfo($clientId, $topic, $payload, $isChild);
			$entity->setRetained($retained);

			return $entity;
		}

		if ($this->validator->validateDeviceProperty($topic)) {
			$entity = $this->parseDeviceProperty($clientId, $topic, $payload, $isChild);
			$entity->setRetained($retained);

			return $entity;
		}

		if ($this->validator->validateDeviceControl($topic)) {
			$entity = $this->parseDeviceControl($clientId, $topic, $payload, $isChild);
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
				$entity = $this->parseChannelAttribute($clientId, $device, $parent, $topic, $payload);
				$entity->setRetained($retained);

				return $entity;
			}

			if ($this->validator->validateChannelProperty($topic)) {
				$entity = $this->parseChannelProperty($clientId, $device, $parent, $topic, $payload);
				$entity->setRetained($retained);

				return $entity;
			}

			if ($this->validator->validateChannelControl($topic)) {
				$entity = $this->parseChannelControl($clientId, $device, $parent, $topic, $payload);
				$entity->setRetained($retained);

				return $entity;
			}
		}

		throw new Exceptions\ParseMessageException('Provided topic is not valid');
	}

	/**
	 * @param Uuid\UuidInterface $clientId
	 * @param string $topic
	 * @param string $payload
	 * @param bool $isChild
	 *
	 * @return Entities\DeviceAttribute
	 */
	private function parseDeviceAttribute(
		Uuid\UuidInterface $clientId,
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
			$clientId,
			$device,
			$attribute,
			$payload,
			$parent
		);
	}

	/**
	 * @param Uuid\UuidInterface $clientId
	 * @param string $topic
	 * @param string $payload
	 * @param bool $isChild
	 *
	 * @return Entities\Hardware
	 */
	private function parseDeviceHardwareInfo(
		Uuid\UuidInterface $clientId,
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

		return new Entities\Hardware($clientId, $device, $hardware, Helpers\PayloadHelper::cleanName(strtolower($payload)), $parent);
	}

	/**
	 * @param Uuid\UuidInterface $clientId
	 * @param string $topic
	 * @param string $payload
	 * @param bool $isChild
	 *
	 * @return Entities\Firmware
	 */
	private function parseDeviceFirmwareInfo(
		Uuid\UuidInterface $clientId,
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

		return new Entities\Firmware($clientId, $device, $firmware, Helpers\PayloadHelper::cleanName(strtolower($payload)), $parent);
	}

	/**
	 * @param Uuid\UuidInterface $clientId
	 * @param string $topic
	 * @param string $payload
	 * @param bool $isChild
	 *
	 * @return Entities\DeviceProperty
	 */
	private function parseDeviceProperty(
		Uuid\UuidInterface $clientId,
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

		$entity = new Entities\DeviceProperty($clientId, (string) $device, (string) $property, $parent);

		if ($attribute !== null) {
			$entity->addAttribute(
				new Entities\PropertyAttribute($attribute, Helpers\PayloadHelper::cleanPayload($payload))
			);

		} else {
			$entity->setValue($payload);
		}

		return $entity;
	}

	/**
	 * @param Uuid\UuidInterface $clientId
	 * @param string $topic
	 * @param string $payload
	 * @param bool $isChild
	 *
	 * @return Entities\DeviceControl
	 */
	private function parseDeviceControl(
		Uuid\UuidInterface $clientId,
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

		$control = new Entities\DeviceControl($clientId, (string) $device, (string) $property, $parent);

		if ($attribute === null) {
			$control->setValue($payload);

			return $control;

		} elseif ($attribute === 'schema') {
			$control->setSchema($payload);
		}

		return $control;
	}

	/**
	 * @param Uuid\UuidInterface $clientId
	 * @param string $device
	 * @param string|null $parent
	 * @param string $topic
	 * @param string $payload
	 *
	 * @return Entities\ChannelAttribute
	 */
	private function parseChannelAttribute(
		Uuid\UuidInterface $clientId,
		string $device,
		?string $parent,
		string $topic,
		string $payload
	): Entities\ChannelAttribute {
		preg_match(V1Validator::CHANNEL_ATTRIBUTE_REGEXP, $topic, $matches);
		[, , $channel, $attribute] = $matches;

		return new Entities\ChannelAttribute(
			$clientId,
			$device,
			$channel,
			$attribute,
			$payload,
			$parent
		);
	}

	/**
	 * @param Uuid\UuidInterface $clientId
	 * @param string $device
	 * @param string|null $parent
	 * @param string $topic
	 * @param string $payload
	 *
	 * @return Entities\ChannelProperty
	 */
	private function parseChannelProperty(
		Uuid\UuidInterface $clientId,
		string $device,
		?string $parent,
		string $topic,
		string $payload
	): Entities\ChannelProperty {
		preg_match(V1Validator::CHANNEL_PROPERTY_REGEXP, $topic, $matches);
		[, , $channel, $property, , , $attribute] = $matches + [null, null, null, null, null, null, null];

		$entity = new Entities\ChannelProperty($clientId, $device, (string) $channel, (string) $property, $parent);

		if ($attribute !== null) {
			$entity->addAttribute(
				new Entities\PropertyAttribute($attribute, Helpers\PayloadHelper::cleanPayload($payload))
			);

		} else {
			$entity->setValue($payload);
		}

		return $entity;
	}

	/**
	 * @param Uuid\UuidInterface $clientId
	 * @param string $device
	 * @param string|null $parent
	 * @param string $topic
	 * @param string $payload
	 *
	 * @return Entities\ChannelControl
	 */
	private function parseChannelControl(
		Uuid\UuidInterface $clientId,
		string $device,
		?string $parent,
		string $topic,
		string $payload
	): Entities\ChannelControl {
		preg_match(V1Validator::CHANNEL_CONTROL_REGEXP, $topic, $matches);
		[, , $channel, $property, , , $attribute] = $matches + [null, null, null, null, null, null, null];

		$control = new Entities\ChannelControl($clientId, $device, (string) $channel, (string) $property, $parent);

		if ($attribute === null) {
			$control->setValue($payload);

			return $control;

		} elseif ($attribute === 'schema') {
			$control->setSchema($payload);
		}

		return $control;
	}

}
