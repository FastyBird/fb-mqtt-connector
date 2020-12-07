<?php declare(strict_types = 1);

/**
 * MqttV1Sender.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:MqttPlugin!
 * @subpackage     Senders
 * @since          0.1.0
 *
 * @date           24.02.20
 */

namespace FastyBird\MqttPlugin\Senders;

use BinSoul\Net\Mqtt;
use FastyBird\MqttPlugin;
use FastyBird\MqttPlugin\Entities;
use FastyBird\MqttPlugin\Exceptions;
use IPub\MQTTClient;
use Nette;
use Nette\Utils;
use Psr\Log;
use React\Promise;

/**
 * MQTT api V1 sender
 *
 * @package        FastyBird:MqttPlugin!
 * @subpackage     Senders
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class MqttV1Sender implements ISender
{

	use Nette\SmartObject;

	/**
	 * Exchange topics
	 */
	private const DEVICE_PROPERTY_TOPIC
		= MqttPlugin\Constants::MQTT_API_PREFIX
		. MqttPlugin\Constants::MQTT_API_V1_VERSION_PREFIX
		. MqttPlugin\Constants::MQTT_TOPIC_DELIMITER
		. self::DEVICE_REPLACE_STRING
		. MqttPlugin\Constants::MQTT_TOPIC_DELIMITER
		. '$property'
		. MqttPlugin\Constants::MQTT_TOPIC_DELIMITER
		. self::PROPERTY_REPLACE_STRING
		. MqttPlugin\Constants::MQTT_TOPIC_DELIMITER
		. 'set';

	private const DEVICE_CHILD_PROPERTY_TOPIC
		= MqttPlugin\Constants::MQTT_API_PREFIX
		. MqttPlugin\Constants::MQTT_API_V1_VERSION_PREFIX
		. MqttPlugin\Constants::MQTT_TOPIC_DELIMITER
		. self::PARENT_REPLACE_STRING
		. MqttPlugin\Constants::MQTT_TOPIC_DELIMITER
		. '$child'
		. MqttPlugin\Constants::MQTT_TOPIC_DELIMITER
		. self::DEVICE_REPLACE_STRING
		. MqttPlugin\Constants::MQTT_TOPIC_DELIMITER
		. '$property'
		. MqttPlugin\Constants::MQTT_TOPIC_DELIMITER
		. self::PROPERTY_REPLACE_STRING
		. MqttPlugin\Constants::MQTT_TOPIC_DELIMITER
		. 'set';

	private const DEVICE_CONTROL_SET_TOPIC
		= MqttPlugin\Constants::MQTT_API_PREFIX
		. MqttPlugin\Constants::MQTT_API_V1_VERSION_PREFIX
		. MqttPlugin\Constants::MQTT_TOPIC_DELIMITER
		. self::DEVICE_REPLACE_STRING
		. MqttPlugin\Constants::MQTT_TOPIC_DELIMITER
		. '$control'
		. MqttPlugin\Constants::MQTT_TOPIC_DELIMITER
		. self::CONTROL_REPLACE_STRING
		. MqttPlugin\Constants::MQTT_TOPIC_DELIMITER
		. 'set';

	private const DEVICE_CHILD_CONTROL_SET_TOPIC
		= MqttPlugin\Constants::MQTT_API_PREFIX
		. MqttPlugin\Constants::MQTT_API_V1_VERSION_PREFIX
		. MqttPlugin\Constants::MQTT_TOPIC_DELIMITER
		. self::PARENT_REPLACE_STRING
		. MqttPlugin\Constants::MQTT_TOPIC_DELIMITER
		. '$child'
		. MqttPlugin\Constants::MQTT_TOPIC_DELIMITER
		. self::DEVICE_REPLACE_STRING
		. MqttPlugin\Constants::MQTT_TOPIC_DELIMITER
		. '$control'
		. MqttPlugin\Constants::MQTT_TOPIC_DELIMITER
		. self::CONTROL_REPLACE_STRING
		. MqttPlugin\Constants::MQTT_TOPIC_DELIMITER
		. 'set';

	private const CHANNEL_PROPERTY_TOPIC
		= MqttPlugin\Constants::MQTT_API_PREFIX
		. MqttPlugin\Constants::MQTT_API_V1_VERSION_PREFIX
		. MqttPlugin\Constants::MQTT_TOPIC_DELIMITER
		. self::DEVICE_REPLACE_STRING
		. MqttPlugin\Constants::MQTT_TOPIC_DELIMITER
		. '$channel'
		. MqttPlugin\Constants::MQTT_TOPIC_DELIMITER
		. self::CHANNEL_REPLACE_STRING
		. MqttPlugin\Constants::MQTT_TOPIC_DELIMITER
		. '$property'
		. MqttPlugin\Constants::MQTT_TOPIC_DELIMITER
		. self::PROPERTY_REPLACE_STRING
		. MqttPlugin\Constants::MQTT_TOPIC_DELIMITER
		. 'set';

	private const CHANNEL_CONFIGURE_TOPIC
		= MqttPlugin\Constants::MQTT_API_PREFIX
		. MqttPlugin\Constants::MQTT_API_V1_VERSION_PREFIX
		. MqttPlugin\Constants::MQTT_TOPIC_DELIMITER
		. self::DEVICE_REPLACE_STRING
		. MqttPlugin\Constants::MQTT_TOPIC_DELIMITER
		. '$channel'
		. MqttPlugin\Constants::MQTT_TOPIC_DELIMITER
		. self::CHANNEL_REPLACE_STRING
		. MqttPlugin\Constants::MQTT_TOPIC_DELIMITER
		. '$control'
		. MqttPlugin\Constants::MQTT_TOPIC_DELIMITER
		. self::CONTROL_REPLACE_STRING
		. MqttPlugin\Constants::MQTT_TOPIC_DELIMITER
		. 'set';

	private const CHANNEL_CHILD_PROPERTY_TOPIC
		= MqttPlugin\Constants::MQTT_API_PREFIX
		. MqttPlugin\Constants::MQTT_API_V1_VERSION_PREFIX
		. MqttPlugin\Constants::MQTT_TOPIC_DELIMITER
		. self::PARENT_REPLACE_STRING
		. MqttPlugin\Constants::MQTT_TOPIC_DELIMITER
		. '$child'
		. MqttPlugin\Constants::MQTT_TOPIC_DELIMITER
		. self::DEVICE_REPLACE_STRING
		. MqttPlugin\Constants::MQTT_TOPIC_DELIMITER
		. '$channel'
		. MqttPlugin\Constants::MQTT_TOPIC_DELIMITER
		. self::CHANNEL_REPLACE_STRING
		. MqttPlugin\Constants::MQTT_TOPIC_DELIMITER
		. '$property'
		. MqttPlugin\Constants::MQTT_TOPIC_DELIMITER
		. self::PROPERTY_REPLACE_STRING
		. MqttPlugin\Constants::MQTT_TOPIC_DELIMITER
		. 'set';

	private const CHANNEL_CHILD_CONFIGURE_TOPIC
		= MqttPlugin\Constants::MQTT_API_PREFIX
		. MqttPlugin\Constants::MQTT_API_V1_VERSION_PREFIX
		. MqttPlugin\Constants::MQTT_TOPIC_DELIMITER
		. self::PARENT_REPLACE_STRING
		. MqttPlugin\Constants::MQTT_TOPIC_DELIMITER
		. '$child'
		. MqttPlugin\Constants::MQTT_TOPIC_DELIMITER
		. self::DEVICE_REPLACE_STRING
		. MqttPlugin\Constants::MQTT_TOPIC_DELIMITER
		. '$channel'
		. MqttPlugin\Constants::MQTT_TOPIC_DELIMITER
		. self::CHANNEL_REPLACE_STRING
		. MqttPlugin\Constants::MQTT_TOPIC_DELIMITER
		. '$control'
		. MqttPlugin\Constants::MQTT_TOPIC_DELIMITER
		. self::CONTROL_REPLACE_STRING
		. MqttPlugin\Constants::MQTT_TOPIC_DELIMITER
		. 'set';

	/** @var MQTTClient\Client\IClient */
	private $mqttClient;

	/** @var Log\LoggerInterface */
	private $logger;

	public function __construct(
		MQTTClient\Client\IClient $mqttClient,
		?Log\LoggerInterface $logger = null
	) {
		$this->mqttClient = $mqttClient;
		$this->logger = $logger ?? new Log\NullLogger();
	}

	/**
	 * {@inheritDoc}
	 */
	public function getVersion(): string
	{
		return MqttPlugin\Constants::MQTT_API_VERSION_V1;
	}

	/**
	 * {@inheritDoc}
	 */
	public function sendChannelProperty(
		string $device,
		string $channel,
		string $property,
		string $payload,
		?string $parentDevice = null
	): Promise\ExtendedPromiseInterface {
		return $this->sendToDevice(
			$this->buildChannelPropertyTopic(
				$parentDevice !== null ? self::CHANNEL_CHILD_PROPERTY_TOPIC : self::CHANNEL_PROPERTY_TOPIC,
				$device,
				$channel,
				$property,
				$parentDevice
			),
			$payload
		);
	}

	/**
	 * @param string $topic
	 * @param string|null $payload
	 * @param int $qos
	 * @param bool $retained
	 *
	 * @return Promise\ExtendedPromiseInterface
	 */
	private function sendToDevice(
		string $topic,
		?string $payload,
		int $qos = MqttPlugin\Constants::MQTT_API_QOS_1,
		bool $retained = false
	): Promise\ExtendedPromiseInterface {
		$message = new Mqtt\DefaultMessage(
			$topic,
			$payload ?? '',
			$qos,
			$retained
		);

		$this->logger->info(sprintf(
			'[FB:PLUGIN:MQTT] Published message to topic: %s',
			$message->getTopic()
		), [
			'topic'   => $message->getTopic(),
			'payload' => $message->getPayload(),
			'qos'     => $message->getQosLevel(),
		]);

		return $this->mqttClient
			->publish($message);
	}

	/**
	 * @param string $topic
	 * @param string $device
	 * @param string $channel
	 * @param string $property
	 * @param string|null $parentDevice
	 *
	 * @return string
	 */
	private function buildChannelPropertyTopic(
		string $topic,
		string $device,
		string $channel,
		string $property,
		?string $parentDevice = null
	): string {
		if ($parentDevice !== null) {
			$topic = str_replace(self::PARENT_REPLACE_STRING, $parentDevice, $topic);
		}

		$topic = str_replace(self::DEVICE_REPLACE_STRING, $device, $topic);
		$topic = str_replace(self::CHANNEL_REPLACE_STRING, $channel, $topic);
		$topic = str_replace(self::PROPERTY_REPLACE_STRING, $property, $topic);

		return $topic;
	}

	/**
	 * {@inheritDoc}
	 */
	public function sendChannelConfiguration(
		string $device,
		string $channel,
		Utils\ArrayHash $configuration,
		?string $parentDevice = null
	): Promise\ExtendedPromiseInterface {
		try {
			return $this->sendToDevice(
				$this->buildChannelControlTopic(
					$parentDevice !== null ? self::CHANNEL_CHILD_CONFIGURE_TOPIC : self::CHANNEL_CONFIGURE_TOPIC,
					$device,
					$channel,
					$parentDevice
				),
				Utils\Json::encode((array) $configuration)
			);

		} catch (Utils\JsonException $ex) {
			throw new Exceptions\InvalidArgumentException('Provided payload could not be json-encoded');
		}
	}

	/**
	 * @param string $topic
	 * @param string $device
	 * @param string $channel
	 * @param string|null $parentDevice
	 *
	 * @return string
	 */
	private function buildChannelControlTopic(
		string $topic,
		string $device,
		string $channel,
		?string $parentDevice = null
	): string {
		if ($parentDevice !== null) {
			$topic = str_replace(self::PARENT_REPLACE_STRING, $parentDevice, $topic);
		}

		$topic = str_replace(self::DEVICE_REPLACE_STRING, $device, $topic);
		$topic = str_replace(self::CHANNEL_REPLACE_STRING, $channel, $topic);
		$topic = str_replace(self::CONTROL_REPLACE_STRING, Entities\ChannelControl::CONFIG, $topic);

		return $topic;
	}

	/**
	 * {@inheritDoc}
	 */
	public function sendDeviceProperty(
		string $device,
		string $property,
		string $payload,
		?string $parentDevice = null
	): Promise\ExtendedPromiseInterface {
		return $this->sendToDevice(
			$this->buildDevicePropertyTopic(
				$parentDevice !== null ? self::DEVICE_CHILD_PROPERTY_TOPIC : self::DEVICE_PROPERTY_TOPIC,
				$device,
				$property,
				$parentDevice
			),
			$payload
		);
	}

	/**
	 * @param string $topic
	 * @param string $device
	 * @param string $property
	 * @param string|null $parentDevice
	 *
	 * @return string
	 */
	private function buildDevicePropertyTopic(
		string $topic,
		string $device,
		string $property,
		?string $parentDevice = null
	): string {
		if ($parentDevice !== null) {
			$topic = str_replace(self::PARENT_REPLACE_STRING, $parentDevice, $topic);
		}

		$topic = str_replace(self::DEVICE_REPLACE_STRING, $device, $topic);
		$topic = str_replace(self::PROPERTY_REPLACE_STRING, $property, $topic);

		return $topic;
	}

	/**
	 * {@inheritDoc}
	 */
	public function sendDeviceConfiguration(
		string $device,
		Utils\ArrayHash $configuration,
		?string $parentDevice = null
	): Promise\ExtendedPromiseInterface {
		try {
			return $this->sendToDevice(
				$this->buildDeviceControlTopic(
					$parentDevice !== null ? self::DEVICE_CHILD_CONTROL_SET_TOPIC : self::DEVICE_CONTROL_SET_TOPIC,
					$device,
					Entities\DeviceControl::CONFIG,
					$parentDevice
				),
				Utils\Json::encode((array) $configuration)
			);

		} catch (Utils\JsonException $ex) {
			throw new Exceptions\InvalidArgumentException('Provided payload could not be json-encoded');
		}
	}

	/**
	 * @param string $topic
	 * @param string $device
	 * @param string $control
	 * @param string|null $parentDevice
	 *
	 * @return string
	 */
	private function buildDeviceControlTopic(
		string $topic,
		string $device,
		string $control,
		?string $parentDevice = null
	): string {
		if ($parentDevice !== null) {
			$topic = str_replace(self::PARENT_REPLACE_STRING, $parentDevice, $topic);
		}

		$topic = str_replace(self::DEVICE_REPLACE_STRING, $device, $topic);
		$topic = str_replace(self::CONTROL_REPLACE_STRING, $control, $topic);

		return $topic;
	}

	/**
	 * {@inheritDoc}
	 */
	public function sendDeviceRestart(
		string $device,
		?string $parentDevice = null
	): Promise\ExtendedPromiseInterface {
		return $this->sendToDevice(
			$this->buildDeviceControlTopic(
				$parentDevice !== null ? self::DEVICE_CHILD_CONTROL_SET_TOPIC : self::DEVICE_CONTROL_SET_TOPIC,
				$device,
				Entities\DeviceControl::RESET,
				$parentDevice
			),
			'true'
		);
	}

	/**
	 * {@inheritDoc}
	 */
	public function sendDeviceReconnect(
		string $device,
		?string $parentDevice = null
	): Promise\ExtendedPromiseInterface {
		return $this->sendToDevice(
			$this->buildDeviceControlTopic(
				$parentDevice !== null ? self::DEVICE_CHILD_CONTROL_SET_TOPIC : self::DEVICE_CONTROL_SET_TOPIC,
				$device,
				Entities\DeviceControl::RECONNECT,
				$parentDevice
			),
			'true'
		);
	}

	/**
	 * {@inheritDoc}
	 */
	public function sendDeviceFactoryReset(
		string $device,
		?string $parentDevice = null
	): Promise\ExtendedPromiseInterface {
		return $this->sendToDevice(
			$this->buildDeviceControlTopic(
				$parentDevice !== null ? self::DEVICE_CHILD_CONTROL_SET_TOPIC : self::DEVICE_CONTROL_SET_TOPIC,
				$device,
				Entities\DeviceControl::FACTORY_RESET,
				$parentDevice
			),
			'true'
		);
	}

}
