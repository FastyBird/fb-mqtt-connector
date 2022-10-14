<?php declare(strict_types = 1);

namespace Tests\Cases\Unit\Connector;

use FastyBird\DevicesModule;
use FastyBird\DevicesModule\DataStorage as DevicesModuleDataStorage;
use FastyBird\FbMqttConnector\Connector;
use FastyBird\FbMqttConnector\Exceptions;
use FastyBird\Metadata\Entities as MetadataEntities;
use FastyBird\Metadata\Exceptions as MetadataExceptions;
use League\Flysystem;
use Nette;
use Ramsey\Uuid;
use RuntimeException;
use Tests\Cases\Unit\DbTestCase;
use function assert;

final class ConnectorFactoryTest extends DbTestCase
{

	/**
	 * @throws Exceptions\InvalidArgument
	 * @throws Nette\DI\MissingServiceException
	 * @throws Nette\Utils\JsonException
	 * @throws Flysystem\FilesystemException
	 * @throws RuntimeException
	 */
	public function setUp(): void
	{
		parent::setUp();

		$writer = $this->getContainer()->getByType(DevicesModuleDataStorage\Writer::class);
		$reader = $this->getContainer()->getByType(DevicesModuleDataStorage\Reader::class);

		$writer->write();
		$reader->read();
	}

	/**
	 * @throws Exceptions\InvalidArgument
	 * @throws MetadataExceptions\FileNotFound
	 * @throws Nette\DI\MissingServiceException
	 * @throws RuntimeException
	 */
	public function testCreateConnector(): void
	{
		$connectorsRepository = $this->getContainer()->getByType(
			DevicesModule\Models\DataStorage\ConnectorsRepository::class,
		);

		$factory = $this->getContainer()->getByType(Connector\ConnectorFactory::class);

		$connector = $connectorsRepository->findById(Uuid\Uuid::fromString('17c59Dfa-2edd-438e-8c49f-aa4e38e5a5e'));
		assert($connector instanceof MetadataEntities\DevicesModule\Connector);

		$factory->create($connector);

		$this->expectNotToPerformAssertions();
	}

}
