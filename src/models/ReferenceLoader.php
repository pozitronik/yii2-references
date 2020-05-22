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
	public const REFERENCES_DIRECTORY = '@app/models/references';

	/**
	 * @return Reference[]
	 * @throws ReflectionException
	 * @throws Throwable
	 * @throws InvalidConfigException
	 * @throws UnknownClassException
	 */
	public static function getList():array {
		$baseReferences = [];
		$baseReferencesDir = Yii::getAlias(self::REFERENCES_DIRECTORY);

		if (file_exists($baseReferencesDir)) {//Загрузить базовые модели референсов
			$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($baseReferencesDir), RecursiveIteratorIterator::SELF_FIRST);
			/** @var RecursiveDirectoryIterator $file */
			foreach ($files as $file) {
				if ($file->isFile() && 'php' === $file->getExtension() && null !== $model = ReflectionHelper::LoadClassFromFile($file->getRealPath(), [Reference::class])) {
					$baseReferences[$model->formName()] = $model;
				}
			}
		}
		$moduleReferences = self::GetAllReferences();//загрузить модульные модели референсов
		return array_merge($baseReferences, $moduleReferences);
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