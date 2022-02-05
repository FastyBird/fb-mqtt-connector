<?php declare(strict_types = 1);

/**
 * FbMqttConnector.php
 *
 * @license        More in LICENSE.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:FbMqttConnector!
 * @subpackage     Entities
 * @since          0.4.0
 *
 * @date           23.01.22
 */

namespace FastyBird\FbMqttConnector\Entities;

use Consistence\Doctrine\Enum\EnumAnnotation as Enum;
use Doctrine\ORM\Mapping as ORM;
use FastyBird\DevicesModule\Entities as DevicesModuleEntities;
use FastyBird\FbMqttConnector\Types;
use IPub\DoctrineCrud\Mapping\Annotation as IPubDoctrine;

/**
 * @ORM\Entity
 */
class FbMqttConnector extends DevicesModuleEntities\Connectors\Connector implements IFbMqttConnector
{

	public const CONNECTOR_TYPE = 'fb-mqtt';

	/**
	 * @var string|null
	 * @IPubDoctrine\Crud(is="writable")
	 */
	protected ?string $server = null;

	/**
	 * @var int|null
	 * @IPubDoctrine\Crud(is="writable")
	 */
	protected ?int $port = null;

	/**
	 * @var int|null
	 * @IPubDoctrine\Crud(is="writable")
	 */
	protected ?int $securedPort = null;

	/**
	 * @var string|null
	 * @IPubDoctrine\Crud(is="writable")
	 */
	protected ?string $username = null;

	/**
	 * @var string|null
	 * @IPubDoctrine\Crud(is="writable")
	 */
	protected ?string $password = null;

	/**
	 * @var Types\ProtocolVersionType|null
	 *
	 * @Enum(class=Types\ProtocolVersionType::class)
	 * @IPubDoctrine\Crud(is="writable")
	 */
	protected ?Types\ProtocolVersionType $protocol = null;

	/**
	 * {@inheritDoc}
	 */
	public function getType(): string
	{
		return self::CONNECTOR_TYPE;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setServer(?string $server): void
	{
		$this->setParam('server', $server);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getServer(): string
	{
		$server = $this->getParam('server', '127.0.0.1');

		return $server ?? '127.0.0.1';
	}

	/**
	 * {@inheritDoc}
	 */
	public function setPort(?int $port): void
	{
		$this->setParam('port', $port);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getPort(): int
	{
		$securedPort = $this->getParam('port', 1883);

		return $securedPort === null ? 1883 : intval($securedPort);
	}

	/**
	 * {@inheritDoc}
	 */
	public function setSecuredPort(?int $port): void
	{
		$this->setParam('secured_port', $port);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getSecuredPort(): int
	{
		$securedPort = $this->getParam('secured_port', 8883);

		return $securedPort === null ? 8883 : intval($securedPort);
	}

	/**
	 * {@inheritDoc}
	 */
	public function setUsername(?string $username): void
	{
		$this->setParam('username', $username);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getUsername(): ?string
	{
		return $this->getParam('username');
	}

	/**
	 * {@inheritDoc}
	 */
	public function setPassword(?string $password): void
	{
		$this->setParam('password', $password);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getPassword(): ?string
	{
		return $this->getParam('password');
	}

	/**
	 * {@inheritDoc}
	 */
	public function getProtocol(): Types\ProtocolVersionType
	{
		$protocol = $this->getParam('protocol', Types\ProtocolVersionType::VERSION_1);

		return $protocol === null ? Types\ProtocolVersionType::get(Types\ProtocolVersionType::VERSION_1) : Types\ProtocolVersionType::get($protocol);
	}

	/**
	 * {@inheritDoc}
	 */
	public function setProtocol(?Types\ProtocolVersionType $protocol): void
	{
		$this->setParam('protocol', $protocol);
	}

	/**
	 * {@inheritDoc}
	 */
	public function toArray(): array
	{
		return array_merge(parent::toArray(), [
			'server'       => $this->getServer(),
			'port'         => $this->getPort(),
			'secured_port' => $this->getSecuredPort(),
			'username'     => $this->getUsername(),
			'protocol'     => $this->getProtocol()->getValue(),
		]);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getDiscriminatorName(): string
	{
		return self::CONNECTOR_TYPE;
	}

}
