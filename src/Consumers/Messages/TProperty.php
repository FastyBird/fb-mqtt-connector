<?php declare(strict_types = 1);

/**
 * TProperty.php
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

namespace FastyBird\FbMqttConnector\Consumers\Messages;

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
trait TProperty
{

	/**
	 * @param Entities\Messages\Property $entity
	 *
	 * @return Array<string, string|string[]|float[]|null[]|bool|MetadataTypes\DataTypeType|null>
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
					'queryable' => boolval($attribute->getValue()),
				]);
			}

			if (
				$attribute->getAttribute() === Entities\Messages\PropertyAttribute::DATA_TYPE
				&& MetadataTypes\DataTypeType::isValidValue(strval($attribute->getValue()))
			) {
				$toUpdate = array_merge($toUpdate, [
					'dataType' => MetadataTypes\DataTypeType::get(strval($attribute->getValue())),
				]);
			}

			if ($attribute->getAttribute() === Entities\Messages\PropertyAttribute::FORMAT) {
				$toUpdate = array_merge($toUpdate, [
					'format' => $attribute->getValue(),
				]);
			}

			if ($attribute->getAttribute() === Entities\Messages\PropertyAttribute::UNIT) {
				$toUpdate = array_merge($toUpdate, [
					'unit' => $attribute->getValue(),
				]);
			}
		}

		return $toUpdate;
	}

}
