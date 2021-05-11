<?php
declare(strict_types = 1);

namespace pozitronik\references\models;

use pozitronik\helpers\ArrayHelper;
use pozitronik\helpers\ReflectionHelper;
use ReflectionException;
use Throwable;
use Yii;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\base\UnknownClassException;

/**
 * Class ReferenceLoader
 *
 * @property Reference[] $list [$referenceClassName] => object Reference
 *
 */
class ReferenceLoader extends Model {
	use ReferenceTrait;

	public const REFERENCES_DIRECTORY = '@app/models/references';//можно задать массивом алиасов

	/**
	 * @return Reference[]
	 * @throws ReflectionException
	 * @throws Throwable
	 * @throws InvalidConfigException
	 * @throws UnknownClassException
	 */
	public static function getList():array {
		$baseReferencesDir = ArrayHelper::getValue(Yii::$app->modules, 'references.params.baseDir', self::REFERENCES_DIRECTORY);
		if (is_array($baseReferencesDir)) {
			$baseReferences = [[]];
			foreach ($baseReferencesDir as $referenceDir) {
				$baseReferences[] = self::allDirReferences($referenceDir);
			}
			$baseReferences = array_merge(...$baseReferences);
		} else {
			$baseReferences = self::allDirReferences($baseReferencesDir);
		}

		$moduleReferences = self::GetAllReferences();//загрузить модульные модели референсов
		return array_merge($baseReferences, $moduleReferences);
	}

	/**
	 * @param string $referencesDir
	 * @return Reference[]
	 * @throws ReflectionException
	 * @throws Throwable
	 * @throws UnknownClassException
	 */
	private static function allDirReferences(string $referencesDir):array {
		$baseReferences = [];
		if (false === $baseReferencesDir = Yii::getAlias($referencesDir, false)) return $baseReferences;

		if (file_exists($baseReferencesDir)) {//Загрузить базовые модели референсов
			$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($baseReferencesDir), RecursiveIteratorIterator::SELF_FIRST);
			/** @var RecursiveDirectoryIterator $file */
			foreach ($files as $file) {
				if ($file->isFile() && 'php' === $file->getExtension() && null !== $model = ReflectionHelper::LoadClassFromFile($file->getRealPath(), [Reference::class], false)) {
					$baseReferences[$model->formName()] = $model;
				}
			}
		}
		return $baseReferences;
	}

	/**
	 * @param string $className
	 * @return Reference|null
	 * @throws Throwable
	 */
	public static function getReferenceByClassName(string $className):?Reference {
		return ArrayHelper::getValue(self::getList(), $className);
	}
}