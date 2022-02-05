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
				$subResult = $this->setPropertyName(strval($attribute->getValue()));

				$toUpdate = array_merge($toUpdate, $subResult);
			}

			if ($attribute->getAttribute() === Entities\Messages\PropertyAttribute::SETTABLE) {
				$subResult = $this->setPropertySettable((bool) $attribute->getValue());

				$toUpdate = array_merge($toUpdate, $subResult);
			}

			if ($attribute->getAttribute() === Entities\Messages\PropertyAttribute::QUERYABLE) {
				$subResult = $this->setPropertyQueryable((bool) $entity->getValue());

				$toUpdate = array_merge($toUpdate, $subResult);
			}

			if ($attribute->getAttribute() === Entities\Messages\PropertyAttribute::DATATYPE) {
				$subResult = $this->setPropertyDatatype($entity->getValue());

				$toUpdate = array_merge($toUpdate, $subResult);
			}

			if ($attribute->getAttribute() === Entities\Messages\PropertyAttribute::FORMAT) {
				$subResult = $this->setPropertyFormat($entity->getValue());

				$toUpdate = array_merge($toUpdate, $subResult);
			}

			if ($attribute->getAttribute() === Entities\Messages\PropertyAttribute::SETTABLE) {
				$subResult = $this->setPropertyUnit($entity->getValue());

				$toUpdate = array_merge($toUpdate, $subResult);
			}
		}

		return $toUpdate;
	}

	/**
	 * @param string $name
	 *
	 * @return mixed[]
	 */
	protected function setPropertyName(
		string $name
	): array {
		return [
			'name' => $name,
		];
	}

	/**
	 * @param bool $settable
	 *
	 * @return mixed[]
	 */
	protected function setPropertySettable(
		bool $settable
	): array {
		return [
			'settable' => $settable,
		];
	}

	/**
	 * @param bool $queryable
	 *
	 * @return mixed[]
	 */
	protected function setPropertyQueryable(
		bool $queryable
	): array {
		return [
			'queryable' => $queryable,
		];
	}

	/**
	 * @param string|null $datatype
	 *
	 * @return mixed[]
	 */
	protected function setPropertyDatatype(
		?string $datatype
	): array {
		return [
			'datatype' => $datatype,
		];
	}

	/**
	 * @param string|null $format
	 *
	 * @return mixed[]
	 */
	protected function setPropertyFormat(
		?string $format
	): array {
		return [
			'format' => $format,
		];
	}

	/**
	 * @param string|null $unit
	 *
	 * @return mixed[]
	 */
	protected function setPropertyUnit(
		?string $unit
	): array {
		return [
			'unit' => $unit,
		];
	}

}
