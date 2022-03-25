<?php declare(strict_types = 1);

/**
 * V1Parser.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:FbMqttConnector!
 * @subpackage     API
 * @since          0.1.0
 *
 * @date           24.02.20
 */

namespace FastyBird\FbMqttConnector\API;

use FastyBird\FbMqttConnector\Entities;
use FastyBird\FbMqttConnector\Exceptions;
use FastyBird\FbMqttConnector\Helpers;
use Nette;

/**
 * API v1 topic parser
 *
 * @package        FastyBird:FbMqttConnector!
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
	 * @return Entities\Messages\IEntity
	 */
	public function parse(
		string $topic,
		string $payload,
		bool $retained = false
	): Entities\Messages\IEntity {
		if (!$this->validator->validate($topic)) {
			throw new Exceptions\ParseMessageException('Provided topic is not valid');
		}

		if ($this->validator->validateDeviceAttribute($topic)) {
			$entity = $this->parseDeviceAttribute($topic, $payload);
			$entity->setRetained($retained);

			return $entity;
		}

		if ($this->validator->validateDeviceHardwareInfo($topic)) {
			$entity = $this->parseDeviceHardwareInfo($topic, $payload);
			$entity->setRetained($retained);

			return $entity;
		}

		if ($this->validator->validateDeviceFirmwareInfo($topic)) {
			$entity = $this->parseDeviceFirmwareInfo($topic, $payload);
			$entity->setRetained($retained);

			return $entity;
		}

		if ($this->validator->validateDeviceProperty($topic)) {
			$entity = $this->parseDeviceProperty($topic, $payload);
			$entity->setRetained($retained);

			return $entity;
		}

		if ($this->validator->validateChannelPart($topic)) {
			preg_match(V1Validator::CHANNEL_PARTIAL_REGEXP, $topic, $matches);
			[, $device] = $matches;

			if ($this->validator->validateChannelAttribute($topic)) {
				$entity = $this->parseChannelAttribute($device, $topic, $payload);
				$entity->setRetained($retained);

				return $entity;
			}

			if ($this->validator->validateChannelProperty($topic)) {
				$entity = $this->parseChannelProperty($device, $topic, $payload);
				$entity->setRetained($retained);

				return $entity;
			}
		}

		throw new Exceptions\ParseMessageException('Provided topic is not valid');
	}

	/**
	 * @param string $topic
	 * @param string $payload
	 *
	 * @return Entities\Messages\DeviceAttribute
	 */
	private function parseDeviceAttribute(
		string $topic,
		string $payload
	): Entities\Messages\DeviceAttribute {
		preg_match(V1Validator::DEVICE_ATTRIBUTE_REGEXP, $topic, $matches);
		[, $device, $attribute] = $matches;

		return new Entities\Messages\DeviceAttribute(
			$device,
			$attribute,
			$payload
		);
	}

	/**
	 * @param string $topic
	 * @param string $payload
	 *
	 * @return Entities\Messages\Hardware
	 */
	private function parseDeviceHardwareInfo(
		string $topic,
		string $payload
	): Entities\Messages\Hardware {
		preg_match(V1Validator::DEVICE_HW_INFO_REGEXP, $topic, $matches);
		[, $device, $hardware] = $matches;

		return new Entities\Messages\Hardware($device, $hardware, Helpers\PayloadHelper::cleanName(strtolower($payload)));
	}

	/**
	 * @param string $topic
	 * @param string $payload
	 *
	 * @return Entities\Messages\Firmware
	 */
	private function parseDeviceFirmwareInfo(
		string $topic,
		string $payload
	): Entities\Messages\Firmware {
		preg_match(V1Validator::DEVICE_FW_INFO_REGEXP, $topic, $matches);
		[, $device, $firmware] = $matches;

		return new Entities\Messages\Firmware($device, $firmware, Helpers\PayloadHelper::cleanName(strtolower($payload)));
	}

	/**
	 * @param string $topic
	 * @param string $payload
	 *
	 * @return Entities\Messages\DeviceProperty
	 */
	private function parseDeviceProperty(
		string $topic,
		string $payload
	): Entities\Messages\DeviceProperty {
		preg_match(V1Validator::DEVICE_PROPERTY_REGEXP, $topic, $matches);
		[, $device, $property, , , $attribute] = $matches + [null, null, null, null, null, null];

		$entity = new Entities\Messages\DeviceProperty((string) $device, (string) $property);

		if ($attribute !== null) {
			$entity->addAttribute(
				new Entities\Messages\PropertyAttribute($attribute, Helpers\PayloadHelper::cleanPayload($payload))
			);

		} else {
			$entity->setValue($payload);
		}

		return $entity;
	}

	/**
	 * @param string $device
	 * @param string $topic
	 * @param string $payload
	 *
	 * @return Entities\Messages\ChannelAttribute
	 */
	private function parseChannelAttribute(
		string $device,
		string $topic,
		string $payload
	): Entities\Messages\ChannelAttribute {
		preg_match(V1Validator::CHANNEL_ATTRIBUTE_REGEXP, $topic, $matches);
		[, , $channel, $attribute] = $matches;

		return new Entities\Messages\ChannelAttribute(
			$device,
			$channel,
			$attribute,
			$payload,
		);
	}

	/**
	 * @param string $device
	 * @param string $topic
	 * @param string $payload
	 *
	 * @return Entities\Messages\ChannelProperty
	 */
	private function parseChannelProperty(
		string $device,
		string $topic,
		string $payload
	): Entities\Messages\ChannelProperty {
		preg_match(V1Validator::CHANNEL_PROPERTY_REGEXP, $topic, $matches);
		[, , $channel, $property, , , $attribute] = $matches + [null, null, null, null, null, null, null];

		$entity = new Entities\Messages\ChannelProperty($device, (string) $channel, (string) $property);

		if ($attribute !== null) {
			$entity->addAttribute(
				new Entities\Messages\PropertyAttribute($attribute, Helpers\PayloadHelper::cleanPayload($payload))
			);

		} else {
			$entity->setValue($payload);
		}

		return $entity;
	}

}
