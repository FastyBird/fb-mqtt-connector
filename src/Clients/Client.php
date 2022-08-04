<?php declare(strict_types = 1);

/**
 * Client.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:FbMqttConnector!
 * @subpackage     Clients
 * @since          0.25.0
 *
 * @date           23.02.20
 */

namespace FastyBird\FbMqttConnector\Clients;

use BinSoul\Net\Mqtt;
use Closure;
use FastyBird\DevicesModule\Models as DevicesModuleModels;
use FastyBird\FbMqttConnector;
use FastyBird\FbMqttConnector\Consumers;
use FastyBird\FbMqttConnector\Exceptions;
use FastyBird\FbMqttConnector\Helpers;
use FastyBird\FbMqttConnector\Types;
use FastyBird\Metadata;
use FastyBird\Metadata\Entities as MetadataEntities;
use Nette;
use Psr\Log;
use React\EventLoop;
use React\Promise;
use React\Socket;
use React\Stream;
use Throwable;

/**
 * MQTT client service
 *
 * The following events are emitted:
 *  - onOpen - The network connection to the server is established.
 *  - onClose - The network connection to the server is closed.
 *  - onWarning - An event of severity "warning" occurred.
 *  - onError - An event of severity "error" occurred.
 *  - onConnect - The client connected to the broker.
 *  - onDisconnect - The client disconnected from the broker.
 *  - onSubscribe - The client subscribed to a topic filter.
 *  - onUnsubscribe - The client unsubscribed from topic filter.
 *  - onPublish - A message was published.
 *  - onMessage - A message was received.
 *
 * @package        FastyBird:FbMqttConnector!
 * @subpackage     Clients
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
abstract class Client implements IClient
{

	use Nette\SmartObject;

	/** @var bool */
	protected bool $isConnected = false;

	/** @var bool */
	protected bool $isConnecting = false;

	/** @var bool */
	protected bool $isDisconnecting = false;

	/** @var Closure|null */
	protected ?Closure $onCloseCallback = null;

	/** @var EventLoop\TimerInterface[] */
	protected array $timer = [];

	/** @var Flow[] */
	protected array $receivingFlows = [];

	/** @var Flow[] */
	protected array $sendingFlows = [];

	/** @var Flow|null */
	protected ?Flow $writtenFlow = null;

	/** @var MetadataEntities\Modules\DevicesModule\IConnectorEntity */
	protected MetadataEntities\Modules\DevicesModule\IConnectorEntity $connector;

	/** @var DevicesModuleModels\DataStorage\IConnectorPropertiesRepository */
	protected DevicesModuleModels\DataStorage\IConnectorPropertiesRepository $connectorPropertiesRepository;

	/** @var Helpers\ConnectorHelper */
	protected Helpers\ConnectorHelper $connectorHelper;

	/** @var Consumers\Consumer */
	protected Consumers\Consumer $consumer;

	/** @var EventLoop\LoopInterface */
	protected EventLoop\LoopInterface $eventLoop;

	/** @var Stream\DuplexStreamInterface|null */
	protected ?Stream\DuplexStreamInterface $stream = null;

	/** @var Mqtt\StreamParser */
	protected Mqtt\StreamParser $parser;

	/** @var Mqtt\ClientIdentifierGenerator */
	protected Mqtt\ClientIdentifierGenerator $identifierGenerator;

	/** @var Mqtt\Connection|null */
	protected ?Mqtt\Connection $connection = null;

	/** @var Mqtt\FlowFactory */
	protected Mqtt\FlowFactory $flowFactory;

	/** @var Log\LoggerInterface */
	protected Log\LoggerInterface $logger;

	/**
	 * @param MetadataEntities\Modules\DevicesModule\IConnectorEntity $connector
	 * @param DevicesModuleModels\DataStorage\IConnectorPropertiesRepository $connectorPropertiesRepository
	 * @param Helpers\ConnectorHelper $connectorHelper
	 * @param Consumers\Consumer $consumer
	 * @param EventLoop\LoopInterface $eventLoop
	 * @param Mqtt\ClientIdentifierGenerator|null $identifierGenerator
	 * @param Mqtt\FlowFactory|null $flowFactory
	 * @param Mqtt\StreamParser|null $parser
	 * @param Log\LoggerInterface|null $logger
	 */
	public function __construct(
		MetadataEntities\Modules\DevicesModule\IConnectorEntity $connector,
		DevicesModuleModels\DataStorage\IConnectorPropertiesRepository $connectorPropertiesRepository,
		Helpers\ConnectorHelper $connectorHelper,
		Consumers\Consumer $consumer,
		EventLoop\LoopInterface $eventLoop,
		?Mqtt\ClientIdentifierGenerator $identifierGenerator = null,
		?Mqtt\FlowFactory $flowFactory = null,
		?Mqtt\StreamParser $parser = null,
		?Log\LoggerInterface $logger = null
	) {
		$this->connector = $connector;
		$this->connectorPropertiesRepository = $connectorPropertiesRepository;
		$this->connectorHelper = $connectorHelper;
		$this->consumer = $consumer;
		$this->eventLoop = $eventLoop;

		if ($parser === null) {
			$this->parser = new Mqtt\StreamParser(new Mqtt\DefaultPacketFactory());

		} else {
			$this->parser = $parser;
		}

		$this->parser->onError(function (Throwable $error): void {
			$this->onWarning($error);
		});

		if ($identifierGenerator === null) {
			$this->identifierGenerator = new Mqtt\DefaultIdentifierGenerator();

		} else {
			$this->identifierGenerator = $identifierGenerator;
		}

		if ($flowFactory === null) {
			$this->flowFactory = new Mqtt\DefaultFlowFactory(
				$this->identifierGenerator,
				new Mqtt\DefaultIdentifierGenerator(),
				new Mqtt\DefaultPacketFactory()
			);

		} else {
			$this->flowFactory = $flowFactory;
		}

		$this->logger = $logger ?? new Log\NullLogger();
	}

	/**
	 * {@inheritDoc}
	 */
	public function isConnected(): bool
	{
		return $this->isConnected;
	}

	/**
	 * {@inheritDoc}
	 */
	public function connect(int $timeout = 5): Promise\ExtendedPromiseInterface
	{
		$deferred = new Promise\Deferred();

		if ($this->isConnected || $this->isConnecting) {
			/** @var Promise\ExtendedPromiseInterface $promise */
			$promise = Promise\reject(new Exceptions\LogicException('The client is already connected'));

			return $promise;
		}

		$this->isConnecting = true;
		$this->isConnected = false;

		$username = $this->connectorHelper->getConfiguration(
			$this->connector->getId(),
			Types\ConnectorPropertyIdentifierType::get(Types\ConnectorPropertyIdentifierType::IDENTIFIER_USERNAME)
		);

		$password = $this->connectorHelper->getConfiguration(
			$this->connector->getId(),
			Types\ConnectorPropertyIdentifierType::get(Types\ConnectorPropertyIdentifierType::IDENTIFIER_PASSWORD)
		);

		$server = $this->connectorHelper->getConfiguration(
			$this->connector->getId(),
			Types\ConnectorPropertyIdentifierType::get(Types\ConnectorPropertyIdentifierType::IDENTIFIER_SERVER)
		);

		$port = $this->connectorHelper->getConfiguration(
			$this->connector->getId(),
			Types\ConnectorPropertyIdentifierType::get(Types\ConnectorPropertyIdentifierType::IDENTIFIER_PORT)
		);

		$connection = new Mqtt\DefaultConnection(
			is_string($username) ? $username : '',
			is_string($password) ? $password : '',
			null,
			$this->connector->getId()->toString(),
		);

		if ($connection->getClientID() === '') {
			$connection = $connection->withClientID($this->identifierGenerator->generateClientIdentifier());
		}

		$this->establishConnection(strval($server), intval($port), $timeout)
			->then(function (Stream\DuplexStreamInterface $stream) use ($connection, $deferred, $timeout): void {
				$this->stream = $stream;

				$this->onOpen($connection);

				$this->registerClient($connection, $timeout)
					->then(function ($result) use ($connection, $deferred): void {
						$this->isConnecting = false;
						$this->isConnected = true;
						$this->connection = $connection;

						$this->onConnect($connection);

						$deferred->resolve($result ?? $connection);
					})
					->otherwise(function (Throwable $reason) use ($connection, $deferred): void {
						$this->isConnecting = false;

						$this->onError($reason);
						$deferred->reject($reason);

						$this->stream?->close();

						$this->onClose($connection);
					});
			})
			->otherwise(function (Throwable $reason) use ($deferred): void {
				$this->isConnecting = false;

				$this->onError($reason);
				$deferred->reject($reason);
			});

		/** @var Promise\ExtendedPromiseInterface $promise */
		$promise = $deferred->promise();

		return $promise;
	}

	/**
	 * {@inheritDoc}
	 */
	public function disconnect(int $timeout = 5): Promise\ExtendedPromiseInterface
	{
		if (!$this->isConnected || $this->isDisconnecting || $this->connection === null) {
			/** @var Promise\ExtendedPromiseInterface $promise */
			$promise = Promise\reject(new Exceptions\LogicException('The client is not connected'));

			return $promise;
		}

		$this->isDisconnecting = true;

		$deferred = new Promise\Deferred();

		$isResolved = false;

		/** @var mixed $flowResult */
		$flowResult = null;

		$this->onCloseCallback = function ($connection) use ($deferred, &$isResolved, &$flowResult): void {
			if (!$isResolved) {
				$isResolved = true;

				if ($connection) {
					$this->onDisconnect($connection);
				}

				$deferred->resolve($flowResult ?? $connection);
			}
		};

		$this->startFlow($this->flowFactory->buildOutgoingDisconnectFlow($this->connection), true)
			->then(function ($result) use ($timeout, &$flowResult): void {
				$flowResult = $result;

				$this->timer[] = $this->eventLoop->addTimer(
					$timeout,
					function (): void {
						$this->stream?->close();
					}
				);
			})
			->otherwise(function ($exception) use ($deferred, &$isResolved): void {
				if (!$isResolved) {
					$isResolved = true;
					$this->isDisconnecting = false;
					$deferred->reject($exception);
				}
			});

		/** @var Promise\ExtendedPromiseInterface $promise */
		$promise = $deferred->promise();

		return $promise;
	}

	/**
	 * {@inheritDoc}
	 */
	public function subscribe(Mqtt\Subscription $subscription): Promise\ExtendedPromiseInterface
	{
		if (!$this->isConnected) {
			/** @var Promise\ExtendedPromiseInterface $promise */
			$promise = Promise\reject(new Exceptions\LogicException('The client is not connected'));

			return $promise;
		}

		return $this->startFlow($this->flowFactory->buildOutgoingSubscribeFlow([$subscription]));
	}

	/**
	 * {@inheritDoc}
	 */
	public function unsubscribe(Mqtt\Subscription $subscription): Promise\ExtendedPromiseInterface
	{
		if (!$this->isConnected) {
			/** @var Promise\ExtendedPromiseInterface $promise */
			$promise = Promise\reject(new Exceptions\LogicException('The client is not connected'));

			return $promise;
		}

		$deferred = new Promise\Deferred();

		$this->startFlow($this->flowFactory->buildOutgoingUnsubscribeFlow([$subscription]))
			->then(static function (array $subscriptions) use ($deferred): void {
				$deferred->resolve(array_shift($subscriptions));
			})
			->otherwise(static function ($exception) use ($deferred): void {
				$deferred->reject($exception);
			});

		/** @var Promise\ExtendedPromiseInterface $promise */
		$promise = $deferred->promise();

		return $promise;
	}

	/**
	 * {@inheritDoc}
	 */
	public function publish(
		string $topic,
		?string $payload,
		int $qos = FbMqttConnector\Constants::MQTT_API_QOS_0,
		bool $retain = false
	): Promise\ExtendedPromiseInterface {
		$message = new Mqtt\DefaultMessage($topic, ($payload ?? ''), $qos, $retain);

		if (!$this->isConnected) {
			/** @var Promise\ExtendedPromiseInterface $promise */
			$promise = Promise\reject(new Exceptions\LogicException('The client is not connected'));

			return $promise;
		}

		return $this->startFlow($this->flowFactory->buildOutgoingPublishFlow($message));
	}

	/**
	 * @return void
	 */
	abstract protected function handleCommunication(): void;

	/**
	 * @param Mqtt\Connection $connection
	 *
	 * @return void
	 */
	protected function onOpen(Mqtt\Connection $connection): void
	{
		// Network connection established
		$this->logger->info(
			'Established connection to MQTT broker',
			[
				'source'      => Metadata\Constants::CONNECTOR_FB_MQTT_SOURCE,
				'type'        => 'client',
				'credentials' => [
					'username' => $connection->getUsername(),
				],
			]
		);
	}

	/**
	 * @param Mqtt\Connection $connection
	 *
	 * @return void
	 */
	protected function onClose(Mqtt\Connection $connection): void
	{
		// Network connection closed
		$this->logger->info(
			'Connection to MQTT broker',
			[
				'source'      => Metadata\Constants::CONNECTOR_FB_MQTT_SOURCE,
				'type'        => 'client',
				'credentials' => [
					'username' => $connection->getUsername(),
				],
			]
		);

		$this->eventLoop->stop();
	}

	/**
	 * @param Mqtt\Connection $connection
	 *
	 * @return void
	 */
	protected function onConnect(Mqtt\Connection $connection): void
	{
		// Broker connected
		$this->logger->info(
			sprintf('Connected to MQTT broker with client id %s', $connection->getClientID()),
			[
				'source'      => Metadata\Constants::CONNECTOR_FB_MQTT_SOURCE,
				'type'        => 'client',
				'credentials' => [
					'username' => $connection->getUsername(),
				],
			]
		);

		$this->eventLoop->addPeriodicTimer(0.01, function (): void {
			$this->handleCommunication();
		});

		$this->eventLoop->addPeriodicTimer(0.01, function (): void {
			$this->consumer->consume();
		});
	}

	/**
	 * @param Mqtt\Connection $connection
	 *
	 * @return void
	 */
	protected function onDisconnect(Mqtt\Connection $connection): void
	{
		// Broker disconnected
		$this->logger->info(
			sprintf('Disconnected from MQTT broker with client id %s', $connection->getClientID()),
			[
				'source'      => Metadata\Constants::CONNECTOR_FB_MQTT_SOURCE,
				'type'        => 'client',
				'credentials' => [
					'username' => $connection->getUsername(),
				],
			]
		);
	}

	/**
	 * @param Throwable $ex
	 *
	 * @return void
	 */
	protected function onWarning(Throwable $ex): void
	{
		// Broker warning occur
		$this->logger->warning(
			sprintf('There was an error  %s', $ex->getMessage()),
			[
				'source' => Metadata\Constants::CONNECTOR_FB_MQTT_SOURCE,
				'type'   => 'client',
				'error'  => [
					'message' => $ex->getMessage(),
					'code'    => $ex->getCode(),
				],
			]
		);

		$this->eventLoop->stop();
	}

	/**
	 * @param Throwable $ex
	 *
	 * @return void
	 */
	protected function onError(Throwable $ex): void
	{
		// Broker error occur
		$this->logger->error(
			sprintf('There was an error  %s', $ex->getMessage()),
			[
				'source' => Metadata\Constants::CONNECTOR_FB_MQTT_SOURCE,
				'type'   => 'client',
				'error'  => [
					'message' => $ex->getMessage(),
					'code'    => $ex->getCode(),
				],
			]
		);

		$this->eventLoop->stop();
	}

	/**
	 * @param Mqtt\Message $message
	 *
	 * @return void
	 */
	protected function onMessage(Mqtt\Message $message): void
	{
		// Broker send message
		$this->logger->info(
			sprintf(
				'Received message in topic: %s with payload %s',
				$message->getTopic(),
				$message->getPayload()
			),
			[
				'source'  => Metadata\Constants::CONNECTOR_FB_MQTT_SOURCE,
				'type'    => 'client',
				'message' => [
					'topic'      => $message->getTopic(),
					'payload'    => $message->getPayload(),
					'isRetained' => $message->isRetained(),
					'qos'        => $message->getQosLevel(),
				],
			]
		);
	}

	/**
	 * @param Mqtt\Subscription $subscription
	 *
	 * @return void
	 */
	protected function onSubscribe(Mqtt\Subscription $subscription): void
	{
		// TODO: Implement onSubscribe() method.
	}

	/**
	 * @param Mqtt\Subscription[] $subscriptions
	 *
	 * @return void
	 */
	protected function onUnsubscribe(array $subscriptions): void
	{
		// TODO: Implement onUnsubscribe() method.
	}

	/**
	 * @param Mqtt\Message $message
	 *
	 * @return void
	 */
	protected function onPublish(Mqtt\Message $message): void
	{
		// TODO: Implement onPublish() method.
	}

	/**
	 * Establishes a network connection to a server
	 *
	 * @param string $host
	 * @param int $port
	 * @param int $timeout
	 *
	 * @return Promise\ExtendedPromiseInterface
	 */
	private function establishConnection(string $host, int $port, int $timeout): Promise\ExtendedPromiseInterface
	{
		$deferred = new Promise\Deferred();

		$future = null;

		$timer = $this->eventLoop->addTimer(
			$timeout,
			static function () use ($deferred, $timeout, &$future): void {
				$exception = new Exceptions\RuntimeException(sprintf('Connection timed out after %d seconds', $timeout));
				$deferred->reject($exception);

				/** @phpstan-ignore-next-line */
				if ($future instanceof Promise\CancellablePromiseInterface) {
					$future->cancel();
				}

				$future = null;
			}
		);

		$future = $this->getConnector()->connect($host . ':' . $port);

		if ($future instanceof Promise\ExtendedPromiseInterface) {
			$future
				->always(function () use ($timer): void {
					$this->eventLoop->cancelTimer($timer);
				})
				->then(function (Stream\DuplexStreamInterface $stream) use ($deferred): void {
					$stream->on('data', function ($data): void {
						$this->handleReceive($data);
					});

					$stream->on('close', function (): void {
						$this->handleClose();
					});

					$stream->on('error', function (Throwable $error): void {
						$this->handleError($error);
					});

					$deferred->resolve($stream);
				})
				->otherwise(static function (Throwable $reason) use ($deferred): void {
					$deferred->reject($reason);
				});
		}

		/** @var Promise\ExtendedPromiseInterface $promise */
		$promise = $deferred->promise();

		return $promise;
	}

	/**
	 * Registers a new client with the broker
	 *
	 * @param Mqtt\Connection $connection
	 * @param int $timeout
	 *
	 * @return Promise\ExtendedPromiseInterface
	 */
	private function registerClient(Mqtt\Connection $connection, int $timeout): Promise\ExtendedPromiseInterface
	{
		$deferred = new Promise\Deferred();

		$responseTimer = $this->eventLoop->addTimer(
			$timeout,
			static function () use ($deferred, $timeout): void {
				$exception = new Exceptions\RuntimeException(sprintf('No response after %d seconds', $timeout));
				$deferred->reject($exception);
			}
		);

		$this->startFlow($this->flowFactory->buildOutgoingConnectFlow($connection), true)
			->always(function () use ($responseTimer): void {
				$this->eventLoop->cancelTimer($responseTimer);
			})
			->then(function ($result) use ($connection, $deferred): void {
				$this->timer[] = $this->eventLoop->addPeriodicTimer(
					floor($connection->getKeepAlive() * 0.75),
					function (): void {
						$this->startFlow($this->flowFactory->buildOutgoingPingFlow());
					}
				);

				$deferred->resolve($result ?? $connection);
			})
			->otherwise(static function (Throwable $reason) use ($deferred): void {
				$deferred->reject($reason);
			});

		/** @var Promise\ExtendedPromiseInterface $promise */
		$promise = $deferred->promise();

		return $promise;
	}

	/**
	 * Handles incoming data
	 *
	 * @param string $data
	 *
	 * @return void
	 */
	private function handleReceive(string $data): void
	{
		if (!$this->isConnected && !$this->isConnecting) {
			return;
		}

		$flowCount = count($this->receivingFlows);

		$packets = $this->parser->push($data);

		foreach ($packets as $packet) {
			$this->handlePacket($packet);
		}

		if ($flowCount > count($this->receivingFlows)) {
			$this->receivingFlows = array_values($this->receivingFlows);
		}

		$this->handleSend();
	}

	/**
	 * Handles an incoming packet
	 *
	 * @param Mqtt\Packet $packet
	 *
	 * @return void
	 */
	private function handlePacket(Mqtt\Packet $packet): void
	{
		switch ($packet->getPacketType()) {
			case Mqtt\Packet::TYPE_PUBLISH:
				if (!($packet instanceof Mqtt\Packet\PublishRequestPacket)) {
					throw new Exceptions\RuntimeException(sprintf('Expected %s but got %s', Mqtt\Packet\PublishRequestPacket::class, get_class($packet)));
				}

				$message = new Mqtt\DefaultMessage(
					$packet->getTopic(),
					$packet->getPayload(),
					$packet->getQosLevel(),
					$packet->isRetained(),
					$packet->isDuplicate()
				);

				$this->startFlow($this->flowFactory->buildIncomingPublishFlow($message, $packet->getIdentifier()));
				break;

			case Mqtt\Packet::TYPE_CONNACK:
			case Mqtt\Packet::TYPE_PINGRESP:
			case Mqtt\Packet::TYPE_SUBACK:
			case Mqtt\Packet::TYPE_UNSUBACK:
			case Mqtt\Packet::TYPE_PUBREL:
			case Mqtt\Packet::TYPE_PUBACK:
			case Mqtt\Packet::TYPE_PUBREC:
			case Mqtt\Packet::TYPE_PUBCOMP:
				$flowFound = false;

				foreach ($this->receivingFlows as $index => $flow) {
					if ($flow->accept($packet)) {
						$flowFound = true;

						unset($this->receivingFlows[$index]);
						$this->continueFlow($flow, $packet);

						break;
					}
				}

				if (!$flowFound) {
					$this->onWarning(
						new Exceptions\LogicException(sprintf('Received unexpected packet of type %d', $packet->getPacketType()))
					);
				}

				break;

			default:
				$this->onWarning(
					new Exceptions\LogicException(sprintf('Cannot handle packet of type %d', $packet->getPacketType()))
				);
		}
	}

	/**
	 * Handles outgoing packets
	 *
	 * @return void
	 */
	private function handleSend(): void
	{
		$flow = null;

		if ($this->writtenFlow !== null) {
			$flow = $this->writtenFlow;
			$this->writtenFlow = null;
		}

		if (count($this->sendingFlows) > 0 && $this->stream !== null) {
			$this->writtenFlow = array_shift($this->sendingFlows);

			if ($this->writtenFlow !== null) {
				$this->stream->write($this->writtenFlow->getPacket());
			}
		}

		if ($flow !== null) {
			if ($flow->isFinished()) {
				$this->eventLoop->futureTick(function () use ($flow): void {
					$this->finishFlow($flow);
				});

			} else {
				$this->receivingFlows[] = $flow;
			}
		}
	}

	/**
	 * Handles closing of the stream
	 *
	 * @return void
	 */
	private function handleClose(): void
	{
		foreach ($this->timer as $timer) {
			$this->eventLoop->cancelTimer($timer);
		}

		$connection = $this->connection;

		$this->isConnecting = false;
		$this->isDisconnecting = false;
		$this->isConnected = false;
		$this->connection = null;
		$this->stream = null;

		if ($this->onCloseCallback !== null) {
			call_user_func($this->onCloseCallback, $connection);

			$this->onCloseCallback = null;
		}

		if ($connection !== null) {
			$this->onClose($connection);
		}
	}

	/**
	 * Handles errors of the stream
	 *
	 * @param Throwable $error
	 *
	 * @return void
	 */
	private function handleError(Throwable $error): void
	{
		$this->onError($error);
	}

	/**
	 * Starts the given flow
	 *
	 * @param Mqtt\Flow $flow
	 * @param bool $isSilent
	 *
	 * @return Promise\ExtendedPromiseInterface
	 */
	private function startFlow(Mqtt\Flow $flow, bool $isSilent = false): Promise\ExtendedPromiseInterface
	{
		try {
			$packet = $flow->start();

		} catch (Throwable $ex) {
			$this->onError($ex);

			/** @var Promise\ExtendedPromiseInterface $promise */
			$promise = Promise\reject($ex);

			return $promise;
		}

		$deferred = new Promise\Deferred();
		$internalFlow = new Flow($flow, $deferred, $packet, $isSilent);

		if ($packet !== null) {
			if ($this->writtenFlow !== null) {
				$this->sendingFlows[] = $internalFlow;

			} elseif ($this->stream !== null) {
				$this->stream->write($packet);
				$this->writtenFlow = $internalFlow;
				$this->handleSend();
			}
		} else {
			$this->eventLoop->futureTick(function () use ($internalFlow): void {
				$this->finishFlow($internalFlow);
			});
		}

		/** @var Promise\ExtendedPromiseInterface $promise */
		$promise = $deferred->promise();

		return $promise;
	}

	/**
	 * Continues the given flow
	 *
	 * @param Flow $flow
	 * @param Mqtt\Packet $packet
	 *
	 * @return void
	 */
	private function continueFlow(Flow $flow, Mqtt\Packet $packet): void
	{
		try {
			$response = $flow->next($packet);

		} catch (Throwable $t) {
			$this->onError($t);

			return;
		}

		if ($response !== null) {
			if ($this->writtenFlow !== null) {
				$this->sendingFlows[] = $flow;

			} elseif ($this->stream !== null) {
				$this->stream->write($response);
				$this->writtenFlow = $flow;
				$this->handleSend();
			}
		} elseif ($flow->isFinished()) {
			$this->eventLoop->futureTick(function () use ($flow): void {
				$this->finishFlow($flow);
			});
		}
	}

	/**
	 * Finishes the given flow
	 *
	 * @param Flow $flow
	 *
	 * @return void
	 */
	private function finishFlow(Flow $flow): void
	{
		if ($flow->isSuccess()) {
			if (!$flow->isSilent()) {
				switch ($flow->getCode()) {
					case 'pong':
						break;

					case 'connect':
						/** @var Mqtt\Connection $result */
						$result = $flow->getResult();

						$this->onConnect($result);
						break;

					case 'disconnect':
						/** @var Mqtt\Connection $result */
						$result = $flow->getResult();

						$this->onDisconnect($result);
						break;

					case 'message':
						/** @var Mqtt\Message $result */
						$result = $flow->getResult();

						$this->onMessage($result);
						break;

					case 'publish':
						/** @var Mqtt\Message $result */
						$result = $flow->getResult();

						$this->onPublish($result);
						break;

					case 'subscribe':
						/** @var Mqtt\Subscription $result */
						$result = $flow->getResult();

						$this->onSubscribe($result);
						break;

					case 'unsubscribe':
						/** @var Mqtt\Subscription[] $result */
						$result = $flow->getResult();

						$this->onUnsubscribe($result);
						break;
				}
			}

			$flow->getDeferred()->resolve($flow->getResult());

		} else {
			$result = new Exceptions\RuntimeException($flow->getErrorMessage());

			$this->onWarning($result);

			$flow->getDeferred()->reject($result);
		}
	}

	/**
	 * @return Socket\ConnectorInterface
	 */
	private function getConnector(): Socket\ConnectorInterface
	{
		return new Socket\Connector($this->eventLoop);
	}

}
