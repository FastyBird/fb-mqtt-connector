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
use FastyBird\FbMqttConnector\Types;
use Nette;
use Ramsey\Uuid;

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

	/**
	 * @param V1Validator $validator
	 */
	public function __construct(
		V1Validator $validator
	) {
		$this->validator = $validator;
	}

	/**
	 * @param Uuid\UuidInterface $connector
	 * @param string $topic
	 * @param string $payload
	 * @param bool $retained
	 *
	 * @return Entities\Messages\IEntity
	 */
	public function parse(
		Uuid\UuidInterface $connector,
		string $topic,
		string $payload,
		bool $retained = false
	): Entities\Messages\IEntity {
		if (!$this->validator->validate($topic)) {
			throw new Exceptions\ParseMessageException('Provided topic is not valid');
		}

		if ($this->validator->validateDeviceAttribute($topic)) {
			$entity = $this->parseDeviceAttribute($connector, $topic, $payload);
			$entity->setRetained($retained);

			return $entity;
		}

		if ($this->validator->validateDeviceHardwareInfo($topic)) {
			$entity = $this->parseDeviceHardwareInfo($connector, $topic, $payload);
			$entity->setRetained($retained);

			return $entity;
		}

		if ($this->validator->validateDeviceFirmwareInfo($topic)) {
			$entity = $this->parseDeviceFirmwareInfo($connector, $topic, $payload);
			$entity->setRetained($retained);

			return $entity;
		}

		if ($this->validator->validateDeviceProperty($topic)) {
			$entity = $this->parseDeviceProperty($connector, $topic, $payload);
			$entity->setRetained($retained);

			return $entity;
		}

		if ($this->validator->validateChannelPart($topic)) {
			preg_match(V1Validator::CHANNEL_PARTIAL_REGEXP, $topic, $matches);
			[, $device] = $matches;

			if ($this->validator->validateChannelAttribute($topic)) {
				$entity = $this->parseChannelAttribute($connector, $device, $topic, $payload);
				$entity->setRetained($retained);

				return $entity;
			}

			if ($this->validator->validateChannelProperty($topic)) {
				$entity = $this->parseChannelProperty($connector, $device, $topic, $payload);
				$entity->setRetained($retained);

				return $entity;
			}
		}

		throw new Exceptions\ParseMessageException('Provided topic is not valid');
	}

	/**
	 * @param Uuid\UuidInterface $connector
	 * @param string $topic
	 * @param string $payload
	 *
	 * @return Entities\Messages\DeviceAttributeEntity
	 */
	private function parseDeviceAttribute(
		Uuid\UuidInterface $connector,
		string $topic,
		string $payload
	): Entities\Messages\DeviceAttributeEntity {
		preg_match(V1Validator::DEVICE_ATTRIBUTE_REGEXP, $topic, $matches);
		[, $device, $attribute] = $matches;

		return new Entities\Messages\DeviceAttributeEntity(
			$connector,
			$device,
			$attribute,
			$payload
		);
	}

	/**
	 * @param Uuid\UuidInterface $connector
	 * @param string $topic
	 * @param string $payload
	 *
	 * @return Entities\Messages\ExtensionAttributeEntity
	 */
	private function parseDeviceHardwareInfo(
		Uuid\UuidInterface $connector,
		string $topic,
		string $payload
	): Entities\Messages\ExtensionAttributeEntity {
		preg_match(V1Validator::DEVICE_HW_INFO_REGEXP, $topic, $matches);
		[, $device, $hardware] = $matches;

		return new Entities\Messages\ExtensionAttributeEntity(
			$connector,
			$device,
			Types\ExtensionTypeType::get(Types\ExtensionTypeType::EXTENSION_TYPE_FASTYBIRD_HARDWARE),
			$hardware,
			Helpers\PayloadHelper::cleanName(strtolower($payload))
		);
	}

	/**
	 * @param Uuid\UuidInterface $connector
	 * @param string $topic
	 * @param string $payload
	 *
	 * @return Entities\Messages\ExtensionAttributeEntity
	 */
	private function parseDeviceFirmwareInfo(
		Uuid\UuidInterface $connector,
		string $topic,
		string $payload
	): Entities\Messages\ExtensionAttributeEntity {
		preg_match(V1Validator::DEVICE_FW_INFO_REGEXP, $topic, $matches);
		[, $device, $firmware] = $matches;

		return new Entities\Messages\ExtensionAttributeEntity(
			$connector,
			$device,
			Types\ExtensionTypeType::get(Types\ExtensionTypeType::EXTENSION_TYPE_FASTYBIRD_FIRMWARE),
			$firmware,
			Helpers\PayloadHelper::cleanName(strtolower($payload))
		);
	}

	/**
	 * @param Uuid\UuidInterface $connector
	 * @param string $topic
	 * @param string $payload
	 *
	 * @return Entities\Messages\DevicePropertyEntity
	 */
	private function parseDeviceProperty(
		Uuid\UuidInterface $connector,
		string $topic,
		string $payload
	): Entities\Messages\DevicePropertyEntity {
		preg_match(V1Validator::DEVICE_PROPERTY_REGEXP, $topic, $matches);
		[, $device, $property, , , $attribute] = $matches + [null, null, null, null, null, null];

		$entity = new Entities\Messages\DevicePropertyEntity($connector, (string) $device, (string) $property);

		if ($attribute !== null) {
			$entity->addAttribute(
				new Entities\Messages\PropertyAttributeEntity($attribute, Helpers\PayloadHelper::cleanPayload($payload))
			);

		} else {
			$entity->setValue($payload);
		}

		return $entity;
	}

	/**
	 * @param Uuid\UuidInterface $connector
	 * @param string $device
	 * @param string $topic
	 * @param string $payload
	 *
	 * @return Entities\Messages\ChannelAttributeEntity
	 */
	private function parseChannelAttribute(
		Uuid\UuidInterface $connector,
		string $device,
		string $topic,
		string $payload
	): Entities\Messages\ChannelAttributeEntity {
		preg_match(V1Validator::CHANNEL_ATTRIBUTE_REGEXP, $topic, $matches);
		[, , $channel, $attribute] = $matches;

		return new Entities\Messages\ChannelAttributeEntity(
			$connector,
			$device,
			$channel,
			$attribute,
			$payload,
		);
	}

	/**
	 * @param Uuid\UuidInterface $connector
	 * @param string $device
	 * @param string $topic
	 * @param string $payload
	 *
	 * @return Entities\Messages\ChannelPropertyEntity
	 */
	private function parseChannelProperty(
		Uuid\UuidInterface $connector,
		string $device,
		string $topic,
		string $payload
	): Entities\Messages\ChannelPropertyEntity {
		preg_match(V1Validator::CHANNEL_PROPERTY_REGEXP, $topic, $matches);
		[, , $channel, $property, , , $attribute] = $matches + [null, null, null, null, null, null, null];

		$entity = new Entities\Messages\ChannelPropertyEntity(
			$connector,
			$device,
			(string) $channel,
			(string) $property
		);

		if ($attribute !== null) {
			$entity->addAttribute(
				new Entities\Messages\PropertyAttributeEntity($attribute, Helpers\PayloadHelper::cleanPayload($payload))
			);

		} else {
			$entity->setValue($payload);
		}

		return $entity;
	}

}
