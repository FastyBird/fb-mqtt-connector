<?php declare(strict_types = 1);

/**
 * MqttClient.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:MqttConnectorPlugin!
 * @subpackage     Client
 * @since          0.1.0
 *
 * @date           23.02.20
 */

namespace FastyBird\MqttConnectorPlugin\Client;

use BinSoul\Net\Mqtt;
use Closure;
use FastyBird\MqttConnectorPlugin\Exceptions;
use FastyBird\MqttConnectorPlugin\Handlers;
use Nette;
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
 * @package        FastyBird:MqttConnectorPlugin!
 * @subpackage     Client
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
class MqttClient
{

	use Nette\SmartObject;

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

	/** @var Handlers\ClientHandler */
	private Handlers\ClientHandler $handler;

	/** @var ConnectionSettings */
	private ConnectionSettings $connectionSettings;

	/** @var Socket\DnsConnector */
	private Socket\DnsConnector $connector;

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

	public function __construct(
		ConnectionSettings $connectionSettings,
		Handlers\ClientHandler $handler,
		EventLoop\LoopInterface $loop,
		?Mqtt\ClientIdentifierGenerator $identifierGenerator = null,
		?Mqtt\FlowFactory $flowFactory = null,
		?Mqtt\StreamParser $parser = null
	) {
		$this->connectionSettings = $connectionSettings;

		$this->handler = $handler;

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
	 * Return the host
	 *
	 * @return string
	 */
	public function getHost(): string
	{
		return $this->connectionSettings->getHost();
	}

	/**
	 * Return the port
	 *
	 * @return int
	 */
	public function getPort(): int
	{
		return $this->connectionSettings->getPort();
	}

	/**
	 * Return client identifier
	 *
	 * @return string
	 */
	public function getClientId(): string
	{
		return $this->connectionSettings->getClientId();
	}

	/**
	 * @return EventLoop\LoopInterface
	 */
	public function getLoop(): EventLoop\LoopInterface
	{
		return $this->loop;
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
	 * Returns the underlying stream or null if the client is not connected
	 *
	 * @return Stream\DuplexStreamInterface|null
	 */
	public function getStream(): ?Stream\DuplexStreamInterface
	{
		return $this->stream;
	}

	/**
	 * Connects to a broker
	 *
	 * @param int $timeout
	 *
	 * @return Promise\ExtendedPromiseInterface
	 */
	public function connect(
		int $timeout = 5
	): Promise\ExtendedPromiseInterface {
		$deferred = new Promise\Deferred();

		if ($this->isConnected || $this->isConnecting) {
			/** @var Promise\ExtendedPromiseInterface $promise */
			$promise = Promise\reject(new Exceptions\LogicException('The client is already connected.'));

			return $promise;
		}

		$this->isConnecting = true;
		$this->isConnected = false;

		$connection = new Mqtt\DefaultConnection(
			$this->connectionSettings->getUsername(),
			$this->connectionSettings->getPassword(),
			$this->connectionSettings->getWill(),
			$this->connectionSettings->getClientId()
		);

		if ($connection->getClientID() === '') {
			$connection = $connection->withClientID($this->identifierGenerator->generateClientIdentifier());
		}

		$this->establishConnection($this->connectionSettings->getHost(), $this->connectionSettings->getPort(), $timeout)
			->then(function (Stream\DuplexStreamInterface $stream) use ($connection, $deferred, $timeout): void {
				$this->stream = $stream;

				$this->handler->onOpen($connection, $this);

				$this->registerClient($connection, $timeout)
					->then(function ($result) use ($connection, $deferred): void {
						$this->isConnecting = false;
						$this->isConnected = true;
						$this->connection = $connection;

						$this->handler->onConnect($connection, $this);

						$deferred->resolve($result ?? $connection);
					})
					->otherwise(function (Throwable $reason) use ($connection, $deferred): void {
						$this->isConnecting = false;

						$this->emitError($reason);
						$deferred->reject($reason);

						if ($this->stream !== null) {
							$this->stream->close();
						}

						$this->handler->onClose($connection, $this);
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
					$this->handler->onDisconnect($connection, $this);
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
	 * Publishes a message
	 *
	 * @param Mqtt\Message $message
	 *
	 * @return Promise\ExtendedPromiseInterface
	 */
	public function publish(Mqtt\Message $message): Promise\ExtendedPromiseInterface
	{
		if (!$this->isConnected) {
			/** @var Promise\ExtendedPromiseInterface $promise */
			$promise = Promise\reject(new Exceptions\LogicException('The client is not connected.'));

			return $promise;
		}

		return $this->startFlow($this->flowFactory->buildOutgoingPublishFlow($message));
	}

	/**
	 * Calls the given generator periodically and publishes the return value
	 *
	 * @param int $interval
	 * @param Mqtt\Message $message
	 * @param callable $generator
	 *
	 * @return Promise\ExtendedPromiseInterface
	 */
	public function publishPeriodically(
		int $interval,
		Mqtt\Message $message,
		callable $generator
	): Promise\ExtendedPromiseInterface {
		if (!$this->isConnected) {
			/** @var Promise\ExtendedPromiseInterface $promise */
			$promise = Promise\reject(new Exceptions\LogicException('The client is not connected.'));

			return $promise;
		}

		$deferred = new Promise\Deferred();

		$this->timer[] = $this->loop->addPeriodicTimer(
			$interval,
			function () use ($message, $generator, $deferred): void {
				$this->publish($message->withPayload((string) $generator($message->getTopic())))
					->then(
						static function ($value) use ($deferred): void {
							$deferred->resolve($value);
						},
						static function (Throwable $reason) use ($deferred): void {
							$deferred->reject($reason);
						}
					);
			}
		);

		/** @var Promise\ExtendedPromiseInterface $promise */
		$promise = $deferred->promise();

		return $promise;
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
		$this->handler->onWarning($error, $this);
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
		$this->handler->onError($error, $this);
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
			$this->handler->onClose($connection, $this);
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
						$this->handler->onConnect($flow->getResult(), $this);
						break;

					case 'disconnect':
						$this->handler->onDisconnect($flow->getResult(), $this);
						break;

					case 'message':
						$this->handler->onMessage($flow->getResult(), $this);
						break;

					case 'publish':
						$this->handler->onPublish($flow->getResult(), $this);
						break;

					case 'subscribe':
						$this->handler->onSubscribe($flow->getResult(), $this);
						break;

					case 'unsubscribe':
						$this->handler->onUnsubscribe($flow->getResult(), $this);
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

}
