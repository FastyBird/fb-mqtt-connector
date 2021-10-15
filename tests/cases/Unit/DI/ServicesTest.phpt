<?php declare(strict_types = 1);

namespace Tests\Cases;

use FastyBird\MqttConnectorPlugin;
use FastyBird\MqttConnectorPlugin\API;
use FastyBird\MqttConnectorPlugin\Consumers;
use FastyBird\MqttConnectorPlugin\Handlers;
use FastyBird\MqttConnectorPlugin\Publishers;
use Tester\Assert;

require_once __DIR__ . '/../../../bootstrap.php';
require_once __DIR__ . '/../BaseTestCase.php';

/**
 * @testCase
 */
final class ServicesTest extends BaseTestCase
{

	public function testServicesRegistration(): void
	{
		$container = $this->createContainer();

		Assert::notNull($container->getByType(API\V1Parser::class));
		Assert::notNull($container->getByType(API\V1Validator::class));

		Assert::notNull($container->getByType(Consumers\Consumer::class));

		Assert::notNull($container->getByType(Handlers\ClientHandler::class));
		Assert::null($container->getByType(Handlers\CommonHandler::class, false));
		Assert::null($container->getByType(Handlers\ApiV1Handler::class, false));

		Assert::notNull($container->getByType(Publishers\Publisher::class));
		Assert::null($container->getByType(Publishers\ApiV1Publisher::class, false));

		Assert::notNull($container->getByType(MqttConnectorPlugin\Client\Client::class));
		Assert::notNull($container->getByType(MqttConnectorPlugin\Client\MqttClientFactory::class));
	}

}

$test_case = new ServicesTest();
$test_case->run();
