<?php declare(strict_types = 1);

/**
 * FbMqttV1Client.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:FbMqttConnector!
 * @subpackage     Client
 * @since          0.1.0
 *
 * @date           23.02.20
 */

namespace FastyBird\FbMqttConnector\Client;

use BinSoul\Net\Mqtt;
use Closure;
use FastyBird\FbMqttConnector;
use FastyBird\FbMqttConnector\API;
use FastyBird\FbMqttConnector\Consumers;
use FastyBird\FbMqttConnector\Entities;
use FastyBird\FbMqttConnector\Exceptions;
use Nette;
use Psr\Log;
use React\EventLoop;
use React\Promise;
use React\Socket;
use React\Stream;
use Throwable;

/**
 * Connects to a MQTT broker and subscribes to topics or publishes messages.
 *
 * The following events are emitted:
 *  - open - The network connection to the server is established.
 *  - close - The network connection to the server is closed.
 *  - warning - An event of severity "warning" occurred.
 *  - error - An event of severity "error" occurred.
 *
 * If a flow finishes it's result is also emitted, e.g.:
 *  - connect - The client connected to the broker.
 *  - disconnect - The client disconnected from the broker.
 *  - subscribe - The client subscribed to a topic filter.
 *  - unsubscribe - The client unsubscribed from topic filter.
 *  - publish - A message was published.
 *  - message - A message was received.
 *
 * @package        FastyBird:FbMqttConnector!
 * @subpackage     Client
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class FbMqttV1Client implements IClient
{

	use Nette\SmartObject;

	public const MQTT_SYSTEM_TOPIC = '$SYS/broker/log/#';

	// MQTT api topics subscribe format
	public const DEVICES_TOPICS = [
		FbMqttConnector\Constants::MQTT_API_PREFIX . FbMqttConnector\Constants::MQTT_API_V1_VERSION_PREFIX . '/+/+',
		FbMqttConnector\Constants::MQTT_API_PREFIX . FbMqttConnector\Constants::MQTT_API_V1_VERSION_PREFIX . '/+/+/+',
		FbMqttConnector\Constants::MQTT_API_PREFIX . FbMqttConnector\Constants::MQTT_API_V1_VERSION_PREFIX . '/+/+/+/+',
		FbMqttConnector\Constants::MQTT_API_PREFIX . FbMqttConnector\Constants::MQTT_API_V1_VERSION_PREFIX . '/+/+/+/+/+',
		FbMqttConnector\Constants::MQTT_API_PREFIX . FbMqttConnector\Constants::MQTT_API_V1_VERSION_PREFIX . '/+/+/+/+/+/+',
		FbMqttConnector\Constants::MQTT_API_PREFIX . FbMqttConnector\Constants::MQTT_API_V1_VERSION_PREFIX . '/+/+/+/+/+/+/+',

		FbMqttConnector\Constants::MQTT_API_PREFIX . FbMqttConnector\Constants::MQTT_API_V1_VERSION_PREFIX . '/+/$child/+/+',
		FbMqttConnector\Constants::MQTT_API_PREFIX . FbMqttConnector\Constants::MQTT_API_V1_VERSION_PREFIX . '/+/$child/+/+/+',
		FbMqttConnector\Constants::MQTT_API_PREFIX . FbMqttConnector\Constants::MQTT_API_V1_VERSION_PREFIX . '/+/$child/+/+/+/+',
		FbMqttConnector\Constants::MQTT_API_PREFIX . FbMqttConnector\Constants::MQTT_API_V1_VERSION_PREFIX . '/+/$child/+/+/+/+/+',
		FbMqttConnector\Constants::MQTT_API_PREFIX . FbMqttConnector\Constants::MQTT_API_V1_VERSION_PREFIX . '/+/$child/+/+/+/+/+/+',
		FbMqttConnector\Constants::MQTT_API_PREFIX . FbMqttConnector\Constants::MQTT_API_V1_VERSION_PREFIX . '/+/$child/+/+/+/+/+/+/+',
	];

	// When new client is connected, broker send specific payload
	private const NEW_CLIENT_MESSAGE_PAYLOAD = 'New client connected from';

	/** @var bool */
	private bool $isConnected = false;

	/** @var bool */
	private bool $isConnecting = false;

	/** @var bool */
	private bool $isDisconnecting = false;

	/** @var Closure|null */
	private ?Closure $onCloseCallback = null;

	/** @var EventLoop\TimerInterface[] */
	private array $timer = [];

	/** @var Flow[] */
	private array $receivingFlows = [];

	/** @var Flow[] */
	private array $sendingFlows = [];

	/** @var Flow|null */
	private ?Flow $writtenFlow;

	/** @var Entities\IFbMqttConnector|null */
	private ?Entities\IFbMqttConnector $connector = null;

	/** @var EventLoop\LoopInterface */
	private EventLoop\LoopInterface $loop;

	/** @var Stream\DuplexStreamInterface|null */
	private ?Stream\DuplexStreamInterface $stream = null;

	/** @var Mqtt\StreamParser */
	private Mqtt\StreamParser $parser;

	/** @var Mqtt\ClientIdentifierGenerator */
	private Mqtt\ClientIdentifierGenerator $identifierGenerator;

	/** @var Mqtt\Connection|null */
	private ?Mqtt\Connection $connection = null;

	/** @var Mqtt\FlowFactory */
	private Mqtt\FlowFactory $flowFactory;

	/** @var API\V1Validator */
	private API\V1Validator $apiValidator;

	/** @var API\V1Parser */
	private API\V1Parser $apiParser;

	/** @var Consumers\IConsumer */
	private Consumers\IConsumer $consumer;

	/** @var Log\LoggerInterface */
	private Log\LoggerInterface $logger;

	public function __construct(
		API\V1Validator $apiValidator,
		API\V1Parser $apiParser,
		Consumers\IConsumer $consumer,
		EventLoop\LoopInterface $loop,
		?Mqtt\ClientIdentifierGenerator $identifierGenerator = null,
		?Mqtt\FlowFactory $flowFactory = null,
		?Mqtt\StreamParser $parser = null
	) {
		$this->apiValidator = $apiValidator;
		$this->apiParser = $apiParser;
		$this->consumer = $consumer;
		$this->loop = $loop;

		if ($parser === null) {
			$this->parser = new Mqtt\StreamParser(new Mqtt\DefaultPacketFactory());

		} else {
			$this->parser = $parser;
		}

		$this->parser->onError(function (Throwable $error): void {
			$this->emitWarning($error);
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
	}

	/**
	 * Indicates if the client is connected
	 *
	 * @return bool
	 */
	public function isConnected(): bool
	{
		return $this->isConnected;
	}

	/**
	 * Connects to a broker
	 *
	 * @param Entities\IFbMqttConnector $connector
	 * @param int $timeout
	 *
	 * @return Promise\ExtendedPromiseInterface
	 */
	public function connect(
		Entities\IFbMqttConnector $connector,
		int $timeout = 5
	): Promise\ExtendedPromiseInterface {
		$this->connector = $connector;

		$deferred = new Promise\Deferred();

		if ($this->isConnected || $this->isConnecting) {
			/** @var Promise\ExtendedPromiseInterface $promise */
			$promise = Promise\reject(new Exceptions\LogicException('The client is already connected.'));

			return $promise;
		}

		$this->isConnecting = true;
		$this->isConnected = false;

		$connection = new Mqtt\DefaultConnection(
			($this->connector->getUsername() ?? ''),
			($this->connector->getPassword() ?? ''),
			null,
			$this->connector->getPlainId()
		);

		if ($connection->getClientID() === '') {
			$connection = $connection->withClientID($this->identifierGenerator->generateClientIdentifier());
		}

		$this->establishConnection($this->connector->getServer(), $this->connector->getPort(), $timeout)
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

						$this->emitError($reason);
						$deferred->reject($reason);

						if ($this->stream !== null) {
							$this->stream->close();
						}

						$this->onClose($connection);
					});
			})
			->otherwise(function (Throwable $reason) use ($deferred): void {
				$this->isConnecting = false;

				$this->emitError($reason);
				$deferred->reject($reason);
			});

		/** @var Promise\ExtendedPromiseInterface $promise */
		$promise = $deferred->promise();

		return $promise;
	}

	/**
	 * Disconnects from a broker
	 *
	 * @param int $timeout
	 *
	 * @return Promise\ExtendedPromiseInterface
	 */
	public function disconnect(int $timeout = 5): Promise\ExtendedPromiseInterface
	{
		if (!$this->isConnected || $this->isDisconnecting || $this->connection === null) {
			/** @var Promise\ExtendedPromiseInterface $promise */
			$promise = Promise\reject(new Exceptions\LogicException('The client is not connected.'));

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

				$this->timer[] = $this->loop->addTimer(
					$timeout,
					function (): void {
						if ($this->stream !== null) {
							$this->stream->close();
						}
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
	 * Subscribes to a topic filter
	 *
	 * @param Mqtt\Subscription $subscription
	 *
	 * @return Promise\ExtendedPromiseInterface
	 */
	public function subscribe(Mqtt\Subscription $subscription): Promise\ExtendedPromiseInterface
	{
		if (!$this->isConnected) {
			/** @var Promise\ExtendedPromiseInterface $promise */
			$promise = Promise\reject(new Exceptions\LogicException('The client is not connected.'));

			return $promise;
		}

		return $this->startFlow($this->flowFactory->buildOutgoingSubscribeFlow([$subscription]));
	}

	/**
	 * Unsubscribes from a topic filter
	 *
	 * @param Mqtt\Subscription $subscription
	 *
	 * @return Promise\ExtendedPromiseInterface
	 */
	public function unsubscribe(Mqtt\Subscription $subscription): Promise\ExtendedPromiseInterface
	{
		if (!$this->isConnected) {
			/** @var Promise\ExtendedPromiseInterface $promise */
			$promise = Promise\reject(new Exceptions\LogicException('The client is not connected.'));

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
			$promise = Promise\reject(new Exceptions\LogicException('The client is not connected.'));

			return $promise;
		}

		return $this->startFlow($this->flowFactory->buildOutgoingPublishFlow($message));
	}

	/**
	 * Emits warning
	 *
	 * @param Throwable $error
	 *
	 * @return void
	 */
	private function emitWarning(Throwable $error): void
	{
		$this->onWarning($error);
	}

	/**
	 * Emits error
	 *
	 * @param Throwable $error
	 *
	 * @return void
	 */
	private function emitError(Throwable $error): void
	{
		$this->onError($error);
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

		$timer = $this->loop->addTimer(
			$timeout,
			static function () use ($deferred, $timeout, &$future): void {
				$exception = new Exceptions\RuntimeException(sprintf('Connection timed out after %d seconds.', $timeout));
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
					$this->loop->cancelTimer($timer);
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

		$responseTimer = $this->loop->addTimer(
			$timeout,
			static function () use ($deferred, $timeout): void {
				$exception = new Exceptions\RuntimeException(sprintf('No response after %d seconds.', $timeout));
				$deferred->reject($exception);
			}
		);

		$this->startFlow($this->flowFactory->buildOutgoingConnectFlow($connection), true)
			->always(function () use ($responseTimer): void {
				$this->loop->cancelTimer($responseTimer);
			})
			->then(function ($result) use ($connection, $deferred): void {
				$this->timer[] = $this->loop->addPeriodicTimer(
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
					throw new Exceptions\RuntimeException(sprintf('Expected %s but got %s.', Mqtt\Packet\PublishRequestPacket::class, get_class($packet)));
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
					$this->emitWarning(
						new Exceptions\LogicException(sprintf('Received unexpected packet of type %d.', $packet->getPacketType()))
					);
				}

				break;

			default:
				$this->emitWarning(
					new Exceptions\LogicException(sprintf('Cannot handle packet of type %d.', $packet->getPacketType()))
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
				$this->loop->futureTick(function () use ($flow): void {
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
			$this->loop->cancelTimer($timer);
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
	 * @return void
	 */
	private function handleError(Throwable $error): void
	{
		$this->emitError($error);
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
			$this->emitError($ex);

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
			$this->loop->futureTick(function () use ($internalFlow): void {
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
			$this->emitError($t);

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
			$this->loop->futureTick(function () use ($flow): void {
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
						$this->onConnect($flow->getResult());
						break;

					case 'disconnect':
						$this->onDisconnect($flow->getResult());
						break;

					case 'message':
						$this->onMessage($flow->getResult());
						break;

					case 'publish':
						$this->onPublish($flow->getResult());
						break;

					case 'subscribe':
						$this->onSubscribe($flow->getResult());
						break;

					case 'unsubscribe':
						$this->onUnsubscribe($flow->getResult());
						break;
				}
			}

			$flow->getDeferred()->resolve($flow->getResult());

		} else {
			$result = new Exceptions\RuntimeException($flow->getErrorMessage());
			$this->emitWarning($result);

			$flow->getDeferred()->reject($result);
		}
	}

	/**
	 * @return Socket\ConnectorInterface
	 */
	private function getConnector(): Socket\ConnectorInterface
	{
		return new Socket\Connector($this->loop);
	}

	/**
	 * @param Mqtt\Connection $connection
	 *
	 * @return void
	 */
	private function onOpen(Mqtt\Connection $connection): void
	{
		// Network connection established
		$this->logger->info(sprintf('[FB:PLUGIN:MQTT] Established connection to MQTT broker'), [
			'credentials' => [
				'username' => $connection->getUsername(),
			],
		]);
	}

	/**
	 * @param Mqtt\Connection $connection
	 *
	 * @return void
	 */
	private function onClose(Mqtt\Connection $connection): void
	{
		// Network connection closed
		$this->logger->info(sprintf('[FB:PLUGIN:MQTT] Connection to MQTT broker'), [
			'credentials' => [
				'username' => $connection->getUsername(),
			],
		]);

		$this->loop->stop();
	}

	/**
	 * @param Mqtt\Connection $connection
	 *
	 * @return void
	 */
	private function onConnect(Mqtt\Connection $connection): void
	{
		// Broker connected
		$this->logger->info(sprintf('[FB:PLUGIN:MQTT] Connected to MQTT broker with client id %s', $connection->getClientID()), [
			'credentials' => [
				'username' => $connection->getUsername(),
			],
		]);

		$topic = new Mqtt\DefaultSubscription(self::MQTT_SYSTEM_TOPIC);

		// Subscribe to system topic
		$this
			->subscribe($topic)
			->done(
				function (Mqtt\Subscription $subscription): void {
					$this->logger->info(sprintf('[FB:PLUGIN:MQTT] Subscribed to: %s', $subscription->getFilter()));
				},
				function (Throwable $ex): void {
					$this->logger->error('[FB:PLUGIN:MQTT] ' . $ex->getMessage(), [
						'exception' => [
							'message' => $ex->getMessage(),
							'code'    => $ex->getCode(),
						],
					]);
				}
			);

		// Get all device topics...
		foreach (self::DEVICES_TOPICS as $topic) {
			$topic = new Mqtt\DefaultSubscription($topic);

			// ...& subscribe to them
			$this
				->subscribe($topic)
				->done(
					function (Mqtt\Subscription $subscription): void {
						$this->logger->info(sprintf('[FB:PLUGIN:MQTT] Subscribed to: %s', $subscription->getFilter()));
					},
					function (Throwable $ex): void {
						$this->logger->error('[FB:PLUGIN:MQTT] ' . $ex->getMessage(), [
							'exception' => [
								'message' => $ex->getMessage(),
								'code'    => $ex->getCode(),
							],
						]);
					}
				);
		}
	}

	/**
	 * @param Mqtt\Connection $connection
	 *
	 * @return void
	 */
	private function onDisconnect(Mqtt\Connection $connection): void
	{
		// Broker disconnected
		$this->logger->info(sprintf('[FB:PLUGIN:MQTT] Disconnected from MQTT broker with client id %s', $connection->getClientID()), [
			'credentials' => [
				'username' => $connection->getUsername(),
			],
		]);
	}

	/**
	 * @param Throwable $ex
	 *
	 * @return void
	 */
	private function onWarning(Throwable $ex): void
	{
		// Broker warning occur
		$this->logger->warning(sprintf('[FB:PLUGIN:MQTT] There was an error  %s', $ex->getMessage()), [
			'error'  => [
				'message' => $ex->getMessage(),
				'code'    => $ex->getCode(),
			],
		]);

		$this->loop->stop();
	}

	/**
	 * @param Throwable $ex
	 *
	 * @return void
	 */
	private function onError(Throwable $ex): void
	{
		// Broker error occur
		$this->logger->error(sprintf('[FB:PLUGIN:MQTT] There was an error  %s', $ex->getMessage()), [
			'error'  => [
				'message' => $ex->getMessage(),
				'code'    => $ex->getCode(),
			],
		]);

		$this->loop->stop();
	}

	/**
	 * @param Mqtt\Message $message
	 *
	 * @return void
	 */
	private function onMessage(Mqtt\Message $message): void
	{
		// Broker send message
		$this->logger->info(sprintf('[FB:PLUGIN:MQTT] Received message in topic: %s with payload %s', $message->getTopic(), $message->getPayload()), [
			'message' => [
				'topic'      => $message->getTopic(),
				'payload'    => $message->getPayload(),
				'isRetained' => $message->isRetained(),
				'qos'        => $message->getQosLevel(),
			],
		]);

		// Check for broker system topic
		if (strpos($message->getTopic(), '$SYS') !== false) {
			[, $param1, $param2, $param3] = explode('/', $message->getTopic()) + [null, null, null, null];

			$payload = $message->getPayload();

			// Broker log
			if ($param1 === 'broker' && $param2 === 'log') {
				switch ($param3) {
					// Notice
					case 'N':
						$this->logger->notice('[FB:PLUGIN:MQTT] ' . $payload);

						// Nev device connected message
						if (strpos($message->getPayload(), self::NEW_CLIENT_MESSAGE_PAYLOAD) !== false) {
							[, , , , , $ipAddress, , $deviceId, , , $username] = explode(' ', $message->getPayload()) + [null, null, null, null, null, null, null, null, null, null, null];

							// Check for correct data
							if ($username !== null && $deviceId !== null && $ipAddress !== null) {
								$entity = new Entities\Messages\DeviceProperty(
									$deviceId,
									'ip-address'
								);
								$entity->setValue($ipAddress);

								$this->consumer->consume($entity);
							}
						}

						break;

					// Error
					case 'E':
						$this->logger->error('[FB:PLUGIN:MQTT] ' . $payload);
						break;

					// Information
					case 'I':
						$this->logger->info('[FB:PLUGIN:MQTT] ' . $payload);
						break;

					default:
						$this->logger->debug('[FB:PLUGIN:MQTT] ' . $param3 . ': ' . $payload);
						break;
				}
			}

			return;
		}

		// Connected device topic
		if (
			$this->apiValidator->validateConvention($message->getTopic())
			&& $this->apiValidator->validateVersion($message->getTopic())
		) {
			// Check if message is sent from broker
			if (!$this->apiValidator->validate($message->getTopic())) {
				return;
			}

			try {
				$entity = $this->apiParser->parse(
					$message->getTopic(),
					$message->getPayload(),
					$message->isRetained()
				);

			} catch (Exceptions\ParseMessageException $ex) {
				$this->logger->debug(
					'[FB:PLUGIN:MQTT] Received message could not be successfully parsed to entity.',
					[
						'exception' => [
							'message' => $ex->getMessage(),
							'code'    => $ex->getCode(),
						],
					]
				);

				return;
			}

			$this->consumer->consume($entity);
		}
	}

	/**
	 * @param Mqtt\Subscription $subscription
	 *
	 * @return void
	 */
	private function onSubscribe(Mqtt\Subscription $subscription): void
	{
		// TODO: Implement onSubscribe() method.
	}

	/**
	 * @param Mqtt\Subscription[] $subscriptions
	 *
	 * @return void
	 */
	private function onUnsubscribe(array $subscriptions): void
	{
		// TODO: Implement onUnsubscribe() method.
	}

	/**
	 * @param Mqtt\Message $message
	 *
	 * @return void
	 */
	private function onPublish(Mqtt\Message $message): void
	{
		// TODO: Implement onPublish() method.
	}

}
