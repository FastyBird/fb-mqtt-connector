<?php declare(strict_types = 1);

/**
 * DeviceHardwareMessageConsumer.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:FbMqttConnector!
 * @subpackage     Consumers
 * @since          0.4.0
 *
 * @date           05.02.22
 */

namespace FastyBird\FbMqttConnector\Consumers;

use Doctrine\DBAL\Connection;
use Doctrine\Persistence;
use FastyBird\DevicesModule\Models as DevicesModuleModels;
use FastyBird\DevicesModule\Queries as DevicesModuleQueries;
use FastyBird\FbMqttConnector\Consumers;
use FastyBird\FbMqttConnector\Entities;
use FastyBird\FbMqttConnector\Exceptions;
use Nette;
use Nette\Utils;
use Psr\Log;
use Throwable;

/**
 * Device hardware MQTT message consumer
 *
 * @package        FastyBird:FbMqttConnector!
 * @subpackage     Consumers
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class DeviceHardwareMessageConsumer implements Consumers\IConsumer
{

	use Nette\SmartObject;

	/** @var DevicesModuleModels\Devices\IDevicesRepository */
	private DevicesModuleModels\Devices\IDevicesRepository $deviceRepository;

	/** @var DevicesModuleModels\Devices\IDevicesManager */
	private DevicesModuleModels\Devices\IDevicesManager $devicesManager;

	/** @var Persistence\ManagerRegistry */
	protected Persistence\ManagerRegistry $managerRegistry;

	/** @var Log\LoggerInterface */
	private Log\LoggerInterface $logger;

	public function __construct(
		DevicesModuleModels\Devices\IDevicesRepository $deviceRepository,
		DevicesModuleModels\Devices\IDevicesManager $devicesManager,
		Persistence\ManagerRegistry $managerRegistry,
		?Log\LoggerInterface $logger = null
	) {
		$this->deviceRepository = $deviceRepository;
		$this->devicesManager = $devicesManager;

		$this->managerRegistry = $managerRegistry;

		$this->logger = $logger ?? new Log\NullLogger();
	}

	/**
	 * {@inheritDoc}
	 *
	 * @throws Exceptions\InvalidStateException
	 */
	public function consume(
		Entities\Messages\IEntity $entity
	): void {
		if (!$entity instanceof Entities\Messages\Hardware) {
			return;
		}

		$findDeviceQuery = new DevicesModuleQueries\FindDevicesQuery();
		$findDeviceQuery->byIdentifier($entity->getDevice());

		$device = $this->deviceRepository->findOneBy($findDeviceQuery);

		if ($device === null) {
			$this->logger->error(
				sprintf('Device "%s" is not registered', $entity->getDevice()),
				[
					'source'    => 'fastybird-fb-mqtt-connector',
					'type'      => 'client',
				]
			);

			return;
		}

		try {
			// Start transaction connection to the database
			$this->getOrmConnection()->beginTransaction();

			$toUpdate = [];

			foreach (
				[
					Entities\Messages\Hardware::MAC_ADDRESS,
					Entities\Messages\Hardware::MANUFACTURER,
					Entities\Messages\Hardware::MODEL,
					Entities\Messages\Hardware::VERSION,
				] as $attribute
			) {
				if ($entity->getParameter() === $attribute) {
					$subResult = $this->setDeviceHardwareInfo($attribute, $entity->getValue());

					$toUpdate = array_merge($toUpdate, $subResult);
				}
			}

			if ($toUpdate !== []) {
				$this->devicesManager->update($device, Utils\ArrayHash::from($toUpdate));
			}

			// Commit all changes into database
			$this->getOrmConnection()->commit();

		} catch (Throwable $ex) {
			// Revert all changes when error occur
			if ($this->getOrmConnection()->isTransactionActive()) {
				$this->getOrmConnection()->rollBack();
			}

			throw new Exceptions\InvalidStateException('An error occurred: ' . $ex->getMessage(), $ex->getCode(), $ex);
		}
	}

	/**
	 * @param string $parameter
	 * @param string $value
	 *
	 * @return mixed[]
	 */
	private function setDeviceHardwareInfo(
		string $parameter,
		string $value
	): array {
		$parametersMapping = [
			Entities\Messages\Hardware::MAC_ADDRESS  => 'hardwareMacAddress',
			Entities\Messages\Hardware::MANUFACTURER => 'hardwareManufacturer',
			Entities\Messages\Hardware::MODEL        => 'hardwareModel',
			Entities\Messages\Hardware::VERSION      => 'hardwareVersion',
		];

		foreach ($parametersMapping as $key => $field) {
			if ($parameter === $key) {
				return [
					$field => $value,
				];
			}
		}

		return [];
	}

	/**
	 * @return Connection
	 */
	private function getOrmConnection(): Connection
	{
		$connection = $this->managerRegistry->getConnection();

		if ($connection instanceof Connection) {
			return $connection;
		}

		throw new Exceptions\RuntimeException('Entity manager could not be loaded');
	}

}
