<?php declare(strict_types = 1);

/**
 * MqttConnectorPluginExtension.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:FbMqttConnectorPlugin!
 * @subpackage     DI
 * @since          0.1.0
 *
 * @date           03.12.20
 */

namespace FastyBird\MqttConnectorPlugin\DI;

use FastyBird\MqttConnectorPlugin\API;
use FastyBird\MqttConnectorPlugin\Client;
use FastyBird\MqttConnectorPlugin\Consumers;
use FastyBird\MqttConnectorPlugin\Handlers;
use FastyBird\MqttConnectorPlugin\Publishers;
use Nette;
use Nette\DI;
use Nette\Schema;
use React\EventLoop;
use stdClass;

/**
 * MQTT connector plugin
 *
 * @package        FastyBird:FbMqttConnectorPlugin!
 * @subpackage     DI
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
class MqttConnectorPluginExtension extends DI\CompilerExtension
{

	/**
	 * @param Nette\Configurator $config
	 * @param string $extensionName
	 *
	 * @return void
	 */
	public static function register(
		Nette\Configurator $config,
		string $extensionName = 'fbMqttConnectorPlugin'
	): void {
		$config->onCompile[] = function (
			Nette\Configurator $config,
			DI\Compiler $compiler
		) use ($extensionName): void {
			$compiler->addExtension($extensionName, new MqttConnectorPluginExtension());
		};
	}

	/**
	 * {@inheritDoc}
	 */
	public function getConfigSchema(): Schema\Schema
	{
		return Schema\Expect::structure([
			'loop' => Schema\Expect::anyOf(Schema\Expect::string(), Schema\Expect::type(DI\Definitions\Statement::class))->nullable(),
		]);
	}

	/**
	 * {@inheritDoc}
	 */
	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();
		/** @var stdClass $configuration */
		$configuration = $this->getConfig();

		if ($configuration->loop === null && $builder->getByType(EventLoop\LoopInterface::class) === null) {
			$builder->addDefinition($this->prefix('client.loop'), new DI\Definitions\ServiceDefinition())
				->setType(EventLoop\LoopInterface::class)
				->setFactory('React\EventLoop\Factory::create');
		}

		$builder->addDefinition($this->prefix('client'), new DI\Definitions\ServiceDefinition())
			->setFactory(Client\Client::class);

		$builder->addDefinition($this->prefix('client.factory'), new DI\Definitions\ServiceDefinition())
			->setFactory(Client\MqttClientFactory::class);

		// MQTT API
		$builder->addDefinition($this->prefix('api.parser'), new DI\Definitions\ServiceDefinition())
			->setType(API\V1Parser::class);

		$builder->addDefinition($this->prefix('api.validator'), new DI\Definitions\ServiceDefinition())
			->setType(API\V1Validator::class);

		// Handlers
		$builder->addDefinition($this->prefix('handler.proxy'), new DI\Definitions\ServiceDefinition())
			->setType(Handlers\ClientHandler::class);

		$builder->addDefinition($this->prefix('handler.common'), new DI\Definitions\ServiceDefinition())
			->setType(Handlers\CommonHandler::class)
			->setAutowired(false);

		$builder->addDefinition($this->prefix('handler.apiV1'), new DI\Definitions\ServiceDefinition())
			->setType(Handlers\ApiV1Handler::class)
			->setAutowired(false);

		// Publishers
		$builder->addDefinition($this->prefix('publisher.proxy'), new DI\Definitions\ServiceDefinition())
			->setType(Publishers\Publisher::class);

		$builder->addDefinition($this->prefix('publisher.apiV1'), new DI\Definitions\ServiceDefinition())
			->setType(Publishers\ApiV1Publisher::class)
			->setAutowired(false);

		// Consumers
		$builder->addDefinition($this->prefix('consumer.proxy'), new DI\Definitions\ServiceDefinition())
			->setType(Consumers\Consumer::class);
	}

	/**
	 * {@inheritDoc}
	 */
	public function beforeCompile(): void
	{
		parent::beforeCompile();

		$builder = $this->getContainerBuilder();

		// Register data consumers

		/** @var string $consumerServiceName */
		$consumerServiceName = $builder->getByType(Consumers\Consumer::class, true);

		/** @var DI\Definitions\ServiceDefinition $consumerService */
		$consumerService = $builder->getDefinition($consumerServiceName);

		$consumersServices = $builder->findByType(Consumers\IConsumer::class);

		foreach ($consumersServices as $service) {
			if ($service->getType() !== Consumers\Consumer::class) {
				$service->setAutowired(false);

				$consumerService->addSetup('?->addConsumer(?)', [
					'@self',
					$service,
				]);
			}
		}

		// Register clients handlers

		/** @var string $clientHandlerServiceName */
		$clientHandlerServiceName = $builder->getByType(Handlers\ClientHandler::class, true);

		/** @var DI\Definitions\ServiceDefinition $clientHandlerService */
		$clientHandlerService = $builder->getDefinition($clientHandlerServiceName);

		$clientHandlerServices = $builder->findByType(Handlers\IHandler::class);

		foreach ($clientHandlerServices as $service) {
			if ($service->getType() !== Handlers\ClientHandler::class) {
				$service->setAutowired(false);

				$clientHandlerService->addSetup('?->addHandler(?)', [
					'@self',
					$service,
				]);
			}
		}

		// Register messages publishers

		/** @var string $publisherServiceName */
		$publisherServiceName = $builder->getByType(Publishers\Publisher::class, true);

		/** @var DI\Definitions\ServiceDefinition $publisherService */
		$publisherService = $builder->getDefinition($publisherServiceName);

		$publishersServices = $builder->findByType(Publishers\IPublisher::class);

		foreach ($publishersServices as $service) {
			if ($service->getType() !== Publishers\Publisher::class) {
				$service->setAutowired(false);

				$publisherService->addSetup('?->addPublisher(?)', [
					'@self',
					$service,
				]);
			}
		}
	}

}
