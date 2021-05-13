<?php
declare(strict_types = 1);

namespace pozitronik\references\models;

use yii\data\DataProviderInterface;
use yii\db\ActiveQuery;

/**
 * Интерфейс справочника
 *
 * @property int id
 * @property string name
 * @property bool deleted
 * @property-read string $ref_name
 * @property-read array $columns
 * @property-read array $view_columns
 * @property-read string|false $form
 * @property-read string|false $indexForm
 * @property-read string $title
 * @property-read int $usedCount
 * @property-read array $searchSort
 * @property-read DataProviderInterface|null $dataProvider
 * @property null|string $moduleId
 */
interface ReferenceInterface {

	/**
	 * Справочникам всегда нужно возвращать массив значений для выбиралок, вот эта функция у них универсальная
	 * @param bool $sort Сортировка выдачи
	 * @return array
	 */
	public static function mapData(bool $sort = false):array;

	/**
	 * Набор колонок для отображения на главной
	 * @return array
	 */
	public function getColumns():array;

	/**
	 * Набор колонок для отображения на странице просмотра
	 * @return array
	 */
	public function getView_columns():array;

	/**
	 * Если в справочнике требуется редактировать поля, кроме обязательных, то функция возвращает путь к встраиваемой вьюхе, иначе к дефолтной
	 * @return string|false
	 */
	public function getForm():string;
	/**
	 * Если в справочнике требуется отобразить полностью собственную главную страницу, то функция возвращает путь к встраиваемой вьюхе, иначе к дефолтной
	 * @return string|false
	 */
	public function getIndexForm():string;

	/**
	 * Возвращает id модуля, добавившего справочник (null, если справочник базовый)
	 * @return string|null
	 */
	public function getModuleId():?string;

	/**
	 * @param string|null $moduleId
	 */
	public function setModuleId(?string $moduleId):void;

	/**
	 * Поиск по справочнику
	 * @param array $params
	 * @return ActiveQuery
	 */
	public function search(array $params):ActiveQuery;

	/**
	 * @return array
	 */
	public function getSearchSort():?array;

	/**
	 * Объединяет две записи справочника (все ссылки на fromId ведут на toId, fromId удаляется)
	 * @param int $fromId
	 * @param int $toId
	 */
	public static function merge(int $fromId, int $toId):void;

	/**
	 * Количество объектов, использующих это значение справочника
	 * @return int
	 */
	public function getUsedCount():int;

	/**
	 * Возвращает набор параметров в виде data-опций, которые виджет выбиралки присунет в селект.
	 * Рекомендуемый способ получения опций через аякс не менее геморроен, но ещё и не работает
	 * @return array
	 */
	public static function dataOptions():array;

	/**
	 * Если справочнику нужно переопределить дефолтный dataProvider
	 * @return DataProviderInterface|null
	 */
	public function getDataProvider():?DataProviderInterface;
}
