<?php
declare(strict_types = 1);

namespace pozitronik\references\models;

use pozitronik\helpers\ArrayHelper;
use pozitronik\helpers\PathHelper;
use pozitronik\helpers\ReflectionHelper;
use pozitronik\references\ReferencesModule;
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
	public const EXCLUDE_DIRECTORY = [];//можно задать массивом алиасов
	public const INCLUDE_MODULES = false;//true - все, false - ни одного, массив - перечисленные

	/**
	 * @return ReferenceInterface[]
	 * @throws ReflectionException
	 * @throws Throwable
	 * @throws InvalidConfigException
	 * @throws UnknownClassException
	 */
	public static function getList():array {
		$baseReferencesDirs = ReferencesModule::param('baseDir', self::REFERENCES_DIRECTORY);
		$excludeReferencesDirs = ReferencesModule::param('excludeDir', self::EXCLUDE_DIRECTORY);
		if (is_string($baseReferencesDirs)) $baseReferencesDirs = [$baseReferencesDirs];
		if (is_string($excludeReferencesDirs)) $excludeReferencesDirs = [$excludeReferencesDirs];

		$baseReferences = [];
		if ($baseReferencesDirs) {
			$baseReferences = [[]];
			foreach ($baseReferencesDirs as $referenceDir) {
				$baseReferences[] = self::allDirReferences($referenceDir, $excludeReferencesDirs);
			}
			$baseReferences = array_merge(...$baseReferences);
		}
		$moduleReferences = [];
		if (false !== $includeModules = ReferencesModule::param('includeModules', self::INCLUDE_MODULES)) {
			$moduleReferences = self::GetAllReferences((true === $includeModules)?null:$includeModules);//загрузить модульные модели референсов
		}

		return array_merge($baseReferences, $moduleReferences);
	}

	/**
	 * @param string $referencesDir
	 * @param string[] $excludedPaths
	 * @return ReferenceInterface[]
	 * @throws ReflectionException
	 * @throws Throwable
	 * @throws UnknownClassException
	 */
	private static function allDirReferences(string $referencesDir, array $excludedPaths):array {
		$baseReferences = [];
		if (false === $baseReferencesDir = Yii::getAlias($referencesDir, false)) return $baseReferences;

		if (file_exists($baseReferencesDir)) {//Загрузить базовые модели референсов
			$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($baseReferencesDir), RecursiveIteratorIterator::SELF_FIRST);
			/** @var RecursiveDirectoryIterator $file */
			foreach ($files as $file) {
				if (!PathHelper::InPath($file->getPath(), $excludedPaths)
					&& $file->isFile() && 'php' === $file->getExtension()
					&& null !== $model = ReflectionHelper::LoadClassFromFile($file->getRealPath(), [ReferenceInterface::class], false)) {
					$baseReferences[$model->formName()] = $model;
				}
			}
		}
		return $baseReferences;
	}

	/**
	 * @param string $className
	 * @return ReferenceInterface|null
	 * @throws Throwable
	 */
	public static function getReferenceByClassName(string $className):?ReferenceInterface {
		return ArrayHelper::getValue(self::getList(), $className);
	}
}