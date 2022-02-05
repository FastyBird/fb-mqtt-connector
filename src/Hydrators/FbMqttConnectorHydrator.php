<?php declare(strict_types = 1);

/**
 * FbMqttConnectorHydrator.php
 *
 * @license        More in LICENSE.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:FbMqttConnector!
 * @subpackage     Hydrators
 * @since          0.4.0
 *
 * @date           07.12.21
 */

namespace FastyBird\FbMqttConnector\Hydrators;

use FastyBird\DevicesModule\Hydrators as DevicesModuleHydrators;
use FastyBird\FbMqttConnector\Entities;
use FastyBird\FbMqttConnector\Types;
use FastyBird\JsonApi\Exceptions as JsonApiExceptions;
use Fig\Http\Message\StatusCodeInterface;
use IPub\JsonAPIDocument;

/**
 * FastyBird MQTT Connector entity hydrator
 *
 * @package        FastyBird:FbMqttConnector!
 * @subpackage     Hydrators
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 *
 * @phpstan-extends DevicesModuleHydrators\Connectors\ConnectorHydrator<Entities\IFbMqttConnector>
 */
final class FbMqttConnectorHydrator extends DevicesModuleHydrators\Connectors\ConnectorHydrator
{

	/** @var string[] */
	protected array $attributes = [
		0 => 'name',
		1 => 'enabled',
		2 => 'server',
		3 => 'port',
		4 => 'username',
		5 => 'password',
		6 => 'protocol',

		'secured_port' => 'securedPort',
	];

	/**
	 * {@inheritDoc}
	 */
	public function getEntityName(): string
	{
		return Entities\FbMqttConnector::class;
	}

	/**
	 * @param JsonAPIDocument\Objects\IStandardObject $attributes
	 *
	 * @return string|null
	 */
	protected function hydrateServerAttribute(JsonAPIDocument\Objects\IStandardObject $attributes): ?string
	{
		if (
			!is_scalar($attributes->get('server'))
			|| (string) $attributes->get('server') === ''
		) {
			return null;
		}

		return (string) $attributes->get('server');
	}

	/**
	 * @param JsonAPIDocument\Objects\IStandardObject $attributes
	 *
	 * @return int|null
	 */
	protected function hydratePortAttribute(JsonAPIDocument\Objects\IStandardObject $attributes): ?int
	{
		if (
			!is_scalar($attributes->get('port'))
			|| (string) $attributes->get('port') === ''
		) {
			return null;
		}

		return (int) $attributes->get('port');
	}

	/**
	 * @param JsonAPIDocument\Objects\IStandardObject $attributes
	 *
	 * @return int|null
	 */
	protected function hydrateSecuredPortAttribute(JsonAPIDocument\Objects\IStandardObject $attributes): ?int
	{
		if (
			!is_scalar($attributes->get('secured_port'))
			|| (string) $attributes->get('secured_port') === ''
		) {
			return null;
		}

		return (int) $attributes->get('secured_port');
	}

	/**
	 * @param JsonAPIDocument\Objects\IStandardObject $attributes
	 *
	 * @return string|null
	 */
	protected function hydrateUsernameAttribute(JsonAPIDocument\Objects\IStandardObject $attributes): ?string
	{
		if (
			!is_scalar($attributes->get('username'))
			|| (string) $attributes->get('username') === ''
		) {
			return null;
		}

		return (string) $attributes->get('username');
	}

	/**
	 * @param JsonAPIDocument\Objects\IStandardObject $attributes
	 *
	 * @return string|null
	 */
	protected function hydratePasswordAttribute(JsonAPIDocument\Objects\IStandardObject $attributes): ?string
	{
		if (
			!is_scalar($attributes->get('password'))
			|| (string) $attributes->get('password') === ''
		) {
			return null;
		}

		return (string) $attributes->get('password');
	}

	/**
	 * @param JsonAPIDocument\Objects\IStandardObject $attributes
	 *
	 * @return Types\ProtocolVersionType|null
	 */
	protected function hydrateProtocolAttribute(JsonAPIDocument\Objects\IStandardObject $attributes): ?Types\ProtocolVersionType
	{
		if (
			!is_scalar($attributes->get('protocol'))
			|| (string) $attributes->get('protocol') === ''
		) {
			return null;
		}

		if (!Types\ProtocolVersionType::isValidValue((int) $attributes->get('protocol'))) {
			throw new JsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
				$this->translator->translate('//fb-mqtt-connector.base.messages.invalidAttribute.heading'),
				$this->translator->translate('//fb-mqtt-connector.base.messages.invalidAttribute.message'),
				[
					'pointer' => 'data/protocol',
				]
			);
		}

		return Types\ProtocolVersionType::get((int) $attributes->get('protocol'));
	}

}
