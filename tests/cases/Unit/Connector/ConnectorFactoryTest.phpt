<?php declare(strict_types = 1);

namespace Tests\Cases;

use FastyBird\DevicesModule;
use FastyBird\DevicesModule\DataStorage as DevicesModuleDataStorage;
use FastyBird\FbMqttConnector\Connector;
use League\Flysystem;
use Mockery;
use Nette\Utils;
use Ramsey\Uuid;
use Tester\Assert;

require_once __DIR__ . '/../../../bootstrap.php';
require_once __DIR__ . '/../BaseTestCase.php';

/**
 * @testCase
 */
final class ConnectorFactoryTest extends BaseTestCase
{

	public function testCreateConnector(): void
	{
		$filesystem = Mockery::mock(Flysystem\Filesystem::class);
		$filesystem
			->shouldReceive('read')
			->withArgs([DevicesModule\Constants::CONFIGURATION_FILE_FILENAME])
			->andReturn(Utils\FileSystem::read('./../../../fixtures/devices-module-data.json'));

		$this->mockContainerService(
			Flysystem\Filesystem::class,
			$filesystem
		);

		/** @var DevicesModuleDataStorage\Reader $reader */
		$reader = $this->container->getByType(DevicesModuleDataStorage\Reader::class);

		/** @var DevicesModule\Models\DataStorage\IConnectorsRepository $connectorsRepository */
		$connectorsRepository = $this->container->getByType(DevicesModule\Models\DataStorage\IConnectorsRepository::class);

		/** @var Connector\ConnectorFactory $factory */
		$factory = $this->container->getByType(Connector\ConnectorFactory::class);

		// Load data storage configuration
		$reader->read();

		$connector = $factory->create($connectorsRepository->findById(Uuid\Uuid::fromString('4af1972f-dead-49a6-95a7-06b03c78ddb2')));

		Assert::type(Connector\Connector::class, $connector);
	}

}

$test_case = new ConnectorFactoryTest();
$test_case->run();
