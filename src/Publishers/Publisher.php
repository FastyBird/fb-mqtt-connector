<?php declare(strict_types = 1);

/**
 * Publisher.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:MqttConnectorPlugin!
 * @subpackage     Publishers
 * @since          0.1.0
 *
 * @date           24.02.20
 */

namespace FastyBird\MqttConnectorPlugin\Publishers;

use Nette;
use Nette\Utils;
use SplObjectStorage;

/**
 * MQTT publisher proxy
 *
 * @package        FastyBird:MqttConnectorPlugin!
 * @subpackage     Publishers
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class Publisher implements IPublisher
{

	use Nette\SmartObject;

	/** @var SplObjectStorage<IPublisher, null> */
	private SplObjectStorage $publishers;

	public function __construct()
	{
		$this->publishers = new SplObjectStorage();
	}

	/**
	 * @param IPublisher $publisher
	 */
	public function addPublisher(IPublisher $publisher): void
	{
		$this->publishers->attach($publisher);
	}

	/**
	 * {@inheritDoc}
	 */
	public function sendDeviceProperty(
		string $device,
		string $property,
		string $payload,
		?string $parentDevice = null
	): void {
		/** @var IPublisher $publisher */
		foreach ($this->getPublishers() as $publisher) {
			$publisher->sendDeviceProperty($device, $property, $payload, $parentDevice);
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function sendDeviceConfiguration(
		string $device,
		Utils\ArrayHash $configuration,
		?string $parentDevice = null
	): void {
		/** @var IPublisher $publisher */
		foreach ($this->getPublishers() as $publisher) {
			$publisher->sendDeviceConfiguration($device, $configuration, $parentDevice);
		}
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
	): void {
		/** @var IPublisher $publisher */
		foreach ($this->getPublishers() as $publisher) {
			$publisher->sendChannelProperty($device, $channel, $property, $payload, $parentDevice);
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function sendChannelConfiguration(
		string $device,
		string $channel,
		Utils\ArrayHash $configuration,
		?string $parentDevice = null
	): void {
		/** @var IPublisher $publisher */
		foreach ($this->getPublishers() as $publisher) {
			$publisher->sendChannelConfiguration($device, $channel, $configuration, $parentDevice);
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function sendDeviceRestart(
		string $device,
		?string $parentDevice = null
	): void {
		/** @var IPublisher $publisher */
		foreach ($this->getPublishers() as $publisher) {
			$publisher->sendDeviceRestart($device, $parentDevice);
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function sendDeviceReconnect(
		string $device,
		?string $parentDevice = null
	): void {
		/** @var IPublisher $publisher */
		foreach ($this->getPublishers() as $publisher) {
			$publisher->sendDeviceReconnect($device, $parentDevice);
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function sendDeviceFactoryReset(
		string $device,
		?string $parentDevice = null
	): void {
		/** @var IPublisher $publisher */
		foreach ($this->getPublishers() as $publisher) {
			$publisher->sendDeviceFactoryReset($device, $parentDevice);
		}
	}

	/**
	 * @return SplObjectStorage<IPublisher, null>
	 */
	private function getPublishers(): SplObjectStorage
	{
		$this->publishers->rewind();

		return $this->publishers;
	}

}
