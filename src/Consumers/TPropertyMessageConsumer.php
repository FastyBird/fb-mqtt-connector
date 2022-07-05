<?php declare(strict_types = 1);

/**
 * TPropertyMessageConsumer.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:FbMqttConnector!
 * @subpackage     Consumers
 * @since          0.4.0
 *
 * @date           05.02.22
 */

namespace FastyBird\FbMqttConnector\Consumers;

use FastyBird\FbMqttConnector\Entities;

/**
 * Property message consumer
 *
 * @package        FastyBird:FbMqttConnector!
 * @subpackage     Consumers
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
trait TPropertyMessageConsumer
{

	/**
	 * @param Entities\Messages\Property $entity
	 *
	 * @return mixed[]
	 */
	protected function handlePropertyConfiguration(
		Entities\Messages\Property $entity
	): array {
		$toUpdate = [];

		foreach ($entity->getAttributes() as $attribute) {
			if ($attribute->getAttribute() === Entities\Messages\PropertyAttribute::NAME) {
				$toUpdate = array_merge($toUpdate, [
					'name' => strval($attribute->getValue()),
				]);
			}

			if ($attribute->getAttribute() === Entities\Messages\PropertyAttribute::SETTABLE) {
				$toUpdate = array_merge($toUpdate, [
					'settable' => boolval($attribute->getValue()),
				]);
			}

			if ($attribute->getAttribute() === Entities\Messages\PropertyAttribute::QUERYABLE) {
				$toUpdate = array_merge($toUpdate, [
					'queryable' => boolval($entity->getValue()),
				]);
			}

			if ($attribute->getAttribute() === Entities\Messages\PropertyAttribute::DATA_TYPE) {
				$toUpdate = array_merge($toUpdate, [
					'dataType' => strval($entity->getValue()),
				]);
			}

			if ($attribute->getAttribute() === Entities\Messages\PropertyAttribute::FORMAT) {
				$toUpdate = array_merge($toUpdate, [
					'format' => $entity->getValue(),
				]);
			}

			if ($attribute->getAttribute() === Entities\Messages\PropertyAttribute::UNIT) {
				$toUpdate = array_merge($toUpdate, [
					'unit' => $entity->getValue(),
				]);
			}
		}

		return $toUpdate;
	}

}
