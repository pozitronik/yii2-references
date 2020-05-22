<?php
declare(strict_types = 1);

namespace pozitronik\references\models;

use pozitronik\core\helpers\ModuleHelper;
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
	 * @param string $moduleId id плагина
	 * @param null|string Имя класса загружаемого справочника
	 * @return ReferenceInterface[]|ReferenceInterface|null
	 * @throws InvalidConfigException
	 * @throws Throwable
	 */
	public static function GetReferences(string $moduleId, ?string $referenceClassName = null) {
		/** @var array $references */
		if ((null !== $modules = ModuleHelper::GetModuleById($moduleId)) && null !== $references = ArrayHelper::getValue($modules->params, 'references')) {
			if (null === $referenceClassName) {//вернуть массив со всеми справочниками
				$result = [];

				foreach ($references as $reference) {
					$referenceObject = Yii::createObject($reference);
					$referenceObject->moduleId = $modules->id;
					$result[] = $referenceObject;
				}
				return $result;
			}

			foreach ($references as $reference) {
				/** @var ReferenceInterface|Model $referenceObject */
				$referenceObject = Yii::createObject($reference);
				if ($referenceClassName === $referenceObject->formName()) {
					$referenceObject->moduleId = $modules->id;
					return $referenceObject;
				}
			}
		}
		return null;
	}

	/**
	 * Возвращает массив справочников, подключаемых в конфигурациях плагинов
	 * @return ReferenceInterface[]
	 * @throws InvalidConfigException
	 * @throws Throwable
	 */
	public static function GetAllReferences():array {
		$result = [];
		foreach (ModuleHelper::ListModules() as $modules) {
			/** @var array $references */
			if (null !== $references = ArrayHelper::getValue($modules->params, 'references')) {
				foreach ($references as $reference) {
					$referenceObject = Yii::createObject($reference);
					$referenceObject->moduleId = $modules->id;
					$result[$referenceObject->formName()] = $referenceObject;
				}
			}
		}
		return $result;
	}
}