<?php
declare(strict_types = 1);

namespace pozitronik\references\models;

use pozitronik\helpers\ModuleHelper;
use pozitronik\helpers\ArrayHelper;
use Throwable;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Model;

/**
 * Trait ReferenceTrait
 * Поддержка справочников внутри модуля
 */
trait ReferenceTrait {

	/**
	 * Возвращает массив моделей справочников, подключаемых в конфигурации модуля, либо одну модель (при задании $referenceClassName)
	 * @param string $moduleId id модуля
	 * @param null|string Имя класса загружаемого справочника
	 * @return ReferenceInterface[]|ReferenceInterface|null
	 * @throws InvalidConfigException
	 * @throws Throwable
	 */
	public static function GetReferences(string $moduleId, ?string $referenceClassName = null) {
		/** @var array $references */
		if ((null !== $module = ModuleHelper::GetModuleById($moduleId)) && null !== $references = ArrayHelper::getValue($module->params, 'references')) {
			if (null === $referenceClassName) {//вернуть массив со всеми справочниками
				$result = [];

				foreach ($references as $reference) {
					$referenceObject = Yii::createObject($reference);
					$referenceObject->moduleId = $module->id;
					$result[] = $referenceObject;
				}
				return $result;
			}

			foreach ($references as $reference) {
				/** @var ReferenceInterface|Model $referenceObject */
				$referenceObject = Yii::createObject($reference);
				if ($referenceClassName === $referenceObject->formName()) {
					$referenceObject->moduleId = $module->id;
					return $referenceObject;
				}
			}
		}
		return null;
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