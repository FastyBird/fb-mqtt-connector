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
use FastyBird\Metadata\Types as MetadataTypes;

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
	 * @param Entities\Messages\PropertyEntity $entity
	 *
	 * @return Array<string, string|string[]|float[]|null[]|bool|MetadataTypes\DataTypeType|null>
	 */
	protected function handlePropertyConfiguration(
		Entities\Messages\PropertyEntity $entity
	): array {
		$toUpdate = [];

		foreach ($entity->getAttributes() as $attribute) {
			if ($attribute->getAttribute() === Entities\Messages\PropertyAttributeEntity::NAME) {
				$toUpdate = array_merge($toUpdate, [
					'name' => strval($attribute->getValue()),
				]);
			}

			if ($attribute->getAttribute() === Entities\Messages\PropertyAttributeEntity::SETTABLE) {
				$toUpdate = array_merge($toUpdate, [
					'settable' => boolval($attribute->getValue()),
				]);
			}

			if ($attribute->getAttribute() === Entities\Messages\PropertyAttributeEntity::QUERYABLE) {
				$toUpdate = array_merge($toUpdate, [
					'queryable' => boolval($attribute->getValue()),
				]);
			}

			if (
				$attribute->getAttribute() === Entities\Messages\PropertyAttributeEntity::DATA_TYPE
				&& MetadataTypes\DataTypeType::isValidValue(strval($attribute->getValue()))
			) {
				$toUpdate = array_merge($toUpdate, [
					'dataType' => MetadataTypes\DataTypeType::get(strval($attribute->getValue())),
				]);
			}

			if ($attribute->getAttribute() === Entities\Messages\PropertyAttributeEntity::FORMAT) {
				$toUpdate = array_merge($toUpdate, [
					'format' => $attribute->getValue(),
				]);
			}

			if ($attribute->getAttribute() === Entities\Messages\PropertyAttributeEntity::UNIT) {
				$toUpdate = array_merge($toUpdate, [
					'unit' => $attribute->getValue(),
				]);
			}
		}

		return $toUpdate;
	}

}
