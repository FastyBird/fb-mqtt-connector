<?php declare(strict_types = 1);

namespace Tests\Cases\Unit\DI;

use FastyBird\FbMqttConnector\API;
use FastyBird\FbMqttConnector\Consumers;
use Nette;
use Tests\Cases\Unit\BaseTestCase;

final class ServicesTest extends BaseTestCase
{

	/**
	 * @throws Nette\DI\MissingServiceException
	 */
	public function testServicesRegistration(): void
	{
		$container = $this->createContainer();

		self::assertNotNull($container->getByType(API\V1Parser::class, false));
		self::assertNotNull($container->getByType(API\V1Validator::class, false));

		self::assertNotNull($container->getByType(Consumers\Messages::class, false));
	}

}
