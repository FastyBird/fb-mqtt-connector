<?php declare(strict_types = 1);

/**
 * MqttPluginExtension.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:MqttPlugin!
 * @subpackage     DI
 * @since          0.1.0
 *
 * @date           03.12.20
 */

namespace FastyBird\MqttPlugin\DI;

use FastyBird\MqttPlugin;
use FastyBird\MqttPlugin\API;
use FastyBird\MqttPlugin\Consumers;
use FastyBird\MqttPlugin\Events;
use FastyBird\MqttPlugin\Senders;
use FastyBird\MqttPlugin\Subscribers;
use IPub\MQTTClient;
use Nette;
use Nette\DI;

/**
 * MQTT client plugin
 *
 * @package        FastyBird:MqttPlugin!
 * @subpackage     DI
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
class MqttPluginExtension extends DI\CompilerExtension
{

	/**
	 * @param Nette\Configurator $config
	 * @param string $extensionName
	 *
	 * @return void
	 */
	public static function register(
		Nette\Configurator $config,
		string $extensionName = 'fbMqttPlugin'
	): void {
		$config->onCompile[] = function (
			Nette\Configurator $config,
			DI\Compiler $compiler
		) use ($extensionName): void {
			$compiler->addExtension($extensionName, new MqttPluginExtension());
		};
	}

	/**
	 * {@inheritDoc}
	 */
	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('client'))
			->setType(MqttPlugin\Client::class);

		$builder->addDefinition($this->prefix('consumer'))
			->setType(Consumers\ExchangeConsumer::class);

		// MQTT API
		$builder->addDefinition($this->prefix('api.parser'))
			->setType(API\V1Parser::class);

		$builder->addDefinition($this->prefix('api.validator'))
			->setType(API\V1Validator::class);

		// Events
		$builder->addDefinition($this->prefix('events.mqtt.close'))
			->setType(Events\MqttClientCloseHandler::class);

		$builder->addDefinition($this->prefix('events.mqtt.connect'))
			->setType(Events\MqttClientConnectHandler::class);

		$builder->addDefinition($this->prefix('events.mqtt.disconnect'))
			->setType(Events\MqttClientDisconnectHandler::class);

		$builder->addDefinition($this->prefix('events.mqtt.error'))
			->setType(Events\MqttClientErrorHandler::class);

		$builder->addDefinition($this->prefix('events.mqtt.open'))
			->setType(Events\MqttClientOpenHandler::class);

		$builder->addDefinition($this->prefix('events.mqtt.warning'))
			->setType(Events\MqttClientWarningHandler::class);

		$builder->addDefinition($this->prefix('events.mqtt.message'))
			->setType(Events\MqttClientMessageHandler::class);

		$builder->addDefinition($this->prefix('events.mqtt.v1.connect'))
			->setType(Events\MqttClientV1ConnectHandler::class);

		$builder->addDefinition($this->prefix('events.mqtt.v1.message'))
			->setType(Events\MqttClientV1MessageHandler::class);

		// Senders
		$builder->addDefinition($this->prefix('sender'))
			->setType(Senders\MqttV1Sender::class);

		// Subscribers
		$builder->addDefinition($this->prefix('subscribers.application'))
			->setType(Subscribers\ApplicationSubscriber::class);
	}

	/**
	 * {@inheritDoc}
	 */
	public function beforeCompile(): void
	{
		parent::beforeCompile();

		$builder = $this->getContainerBuilder();

		$mqttClientServiceName = $builder->getByType(MQTTClient\Client\Client::class, true);

		if ($mqttClientServiceName !== null) {
			$mqttClientService = $builder->getDefinition($mqttClientServiceName);
			assert($mqttClientService instanceof DI\Definitions\ServiceDefinition);

			$mqttClientService->addSetup('$onOpen[]', [$builder->getDefinitionByType(Events\MqttClientOpenHandler::class)]);
			$mqttClientService->addSetup('$onClose[]', [$builder->getDefinitionByType(Events\MqttClientCloseHandler::class)]);
			$mqttClientService->addSetup('$onConnect[]', [$builder->getDefinitionByType(Events\MqttClientConnectHandler::class)]);
			$mqttClientService->addSetup('$onDisconnect[]', [$builder->getDefinitionByType(Events\MqttClientDisconnectHandler::class)]);
			$mqttClientService->addSetup('$onWarning[]', [$builder->getDefinitionByType(Events\MqttClientWarningHandler::class)]);
			$mqttClientService->addSetup('$onError[]', [$builder->getDefinitionByType(Events\MqttClientErrorHandler::class)]);
			$mqttClientService->addSetup('$onMessage[]', [$builder->getDefinitionByType(Events\MqttClientMessageHandler::class)]);
			$mqttClientService->addSetup('$onConnect[]', [$builder->getDefinitionByType(Events\MqttClientV1ConnectHandler::class)]);
			$mqttClientService->addSetup('$onMessage[]', [$builder->getDefinitionByType(Events\MqttClientV1MessageHandler::class)]);
		}

		/** @var string $messagesConsumerServiceName */
		$messagesConsumerServiceName = $builder->getByType(Consumers\ExchangeConsumer::class, true);

		/** @var DI\Definitions\ServiceDefinition $messagesConsumerService */
		$messagesConsumerService = $builder->getDefinition($messagesConsumerServiceName);

		$consumerHandlersServices = $builder->findByType(Consumers\IMessageHandler::class);

		foreach ($consumerHandlersServices as $consumerHandlersService) {
			$messagesConsumerService->addSetup('?->addHandler(?)', [
				'@self',
				$consumerHandlersService,
			]);
		}
	}

}
