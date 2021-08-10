<?php
declare(strict_types = 1);

namespace pozitronik\references\models;

use yii\data\DataProviderInterface;

/**
 * Интерфейс справочника
 *
 * @property int id Обязательное индексное поле ключа
 * @property string name Обязательное поле значения для ключа
 * @property bool deleted Опциональное поле активности записи (если отсутствует в наборе данных, то всегда должно быть false)
 * @property-read string $ref_name Название справочника (используется в глобальном контроллере)
 * @property-read array $columns Набор колонок для отображения на странице просмотра
 * @property-read array $view_columns Набор колонок для отображения на странице просмотра
 * @property-read null|string $form
 * @property-read null|string $indexForm
 * @property-read string $title
 * @property-read null|int $usedCount Количество объектов, использующих это значение справочника, null - неизвестно
 * @property-read null|array $searchSort
 * @property-read DataProviderInterface|null $dataProvider
 * @property null|string $moduleId id модуля, которому принадлежит справочник (null, если справочник базовый).
 */
interface ReferenceInterface {

	/**
	 * Массив значений справочника в id=>name формате (с игнорированием других имеющихся полей).
	 * Нужно для выбиралок.
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
	 * Если в справочнике требуется редактировать поля, кроме обязательных, то функция возвращает путь к встраиваемой вьюхе, иначе к дефолтной.
	 * null - редактирования не предусмотрено
	 * @return null|string
	 */
	public function getForm():?string;

	/**
	 * Если в справочнике требуется отобразить полностью собственную главную страницу, то функция возвращает путь к встраиваемой вьюхе, иначе к дефолтной.
	 * null - собственной индексной страницы не предусмотрено
	 * @return null|string
	 */
	public function getIndexForm():?string;

	/**
	 * Возвращает id модуля, которому принадлежит справочник (null, если справочник базовый).
	 * Можно возвращать любой мнемонический идентификатор.
	 * @return string|null
	 */
	public function getModuleId():?string;

	/**
	 * @param string|null $moduleId
	 */
	public function setModuleId(?string $moduleId):void;

	/**
	 * Поиск по справочнику
	 * null, если не поддерживается
	 * @param array $params
	 * @return null|mixed
	 */
	public function search(array $params);

	/**
	 * Правила сортировки в поиске по справочнику, null - не предусмотрено
	 * @return null|array
	 */
	public function getSearchSort():?array;

	/**
	 * Объединяет две записи справочника (все ссылки на fromId ведут на toId, fromId удаляется)
	 * @param int $fromId
	 * @param int $toId
	 */
	public static function merge(int $fromId, int $toId):void;

	/**
	 * Количество объектов, использующих это значение справочника.
	 * null - неизвестно
	 * @return null|int
	 */
	public function getUsedCount():?int;

	/**
	 * Возвращает набор параметров в виде data-опций, которые виджет выбиралки присунет в селект.
	 * @return array
	 */
	public static function dataOptions():array;

	/**
	 * Если справочнику нужно переопределить дефолтный dataProvider
	 * @return DataProviderInterface|null
	 */
	public function getDataProvider():?DataProviderInterface;

	/**
	 * Создать новую запись в справочнике из массива $data
	 * @param null|array $data
	 * @return bool|null true - успешно, false - ошибка, null - не поддерживается
	 */
	public function createRecord(?array $data):?bool;

	/**
	 * Обновить запись в справочнике из массива $data
	 * @param null|array $data
	 * @return bool|null true - успешно, false - ошибка, null - не поддерживается
	 */
	public function updateRecord(?array $data):?bool;

	/**
	 * Удалить запись
	 * @return bool|null true - успешно, false - ошибка, null - не поддерживается
	 */
	public function deleteRecord():?bool;

	/**
	 * Вернуть запись по ключу, null, если такой записи нет
	 * @param int $id
	 * @return ReferenceInterface|null
	 */
	public static function getRecord(int $id):?self;

	/**
	 * @see Model::formName()
	 */
	public function formName();
}
