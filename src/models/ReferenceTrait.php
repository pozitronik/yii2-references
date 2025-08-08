<?php
declare(strict_types = 1);

namespace pozitronik\references\models;

use pozitronik\helpers\ModuleHelper;
use pozitronik\helpers\ArrayHelper;
use Throwable;
use Yii;
use yii\base\InvalidConfigException;

/**
 * Trait ReferenceTrait
 * Поддержка справочников внутри модуля
 */
trait ReferenceTrait {

	/**
	 * Возвращает массив моделей справочников, подключаемых в конфигурации модуля, либо одну модель (при задании $referenceClassName)
	 * @param string $moduleId id модуля
	 * @param null|string $referenceClassName Имя класса загружаемого справочника
	 * @return ReferenceInterface[]
	 * @throws InvalidConfigException
	 * @throws Throwable
	 */
	public static function GetReferences(string $moduleId, ?string $referenceClassName = null):array {
		$result = [];
		$includedReferencePath = null === $referenceClassName?'params.references':"params.references.{$referenceClassName}";
		$allModules = array_filter(ArrayHelper::getColumn(ModuleHelper::ListModules([$moduleId], false), $includedReferencePath));

		foreach ($allModules as $moduleName => $references) {
			/** @var array $references */
			foreach ($references as $reference) {
				$referenceObject = Yii::createObject($reference);
				$referenceObject->moduleId = $moduleName;
				$result[$referenceObject->formName()] = $referenceObject;
			}
		}
		return $result;
	}

	/**
	 * Возвращает массив справочников, подключаемых в конфигурациях модулей
	 * @param string[]|null $whiteList Массив с перечислением имён модулей, в которых нужно искать справочники, null - искать во всех
	 * @return ReferenceInterface[]
	 * @throws InvalidConfigException
	 * @throws Throwable
	 */
	public static function GetAllReferences(?array $whiteList = null):array {
		$result = [];
		$allModules = array_filter(ArrayHelper::getColumn(ModuleHelper::ListModules($whiteList, false), 'params.references'));
		foreach ($allModules as $moduleName => $references) {
			/** @var array $references */
			foreach ($references as $reference) {
				$referenceObject = Yii::createObject($reference);
				$referenceObject->moduleId = $moduleName;
				$result[$referenceObject->formName()] = $referenceObject;
			}
		}
		return $result;
	}
}