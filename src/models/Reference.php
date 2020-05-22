<?php
/** @noinspection UndetectableTableInspection */
declare(strict_types = 1);

namespace pozitronik\references\models;

use pozitronik\core\models\lcquery\LCQuery;
use pozitronik\core\traits\ARExtended;
use pozitronik\core\interfaces\reference\ReferenceInterface;
use yii\caching\TagDependency;
use yii\data\DataProviderInterface;
use yii\db\ActiveRecord;
use pozitronik\core\models\core_module\CoreModule;
use pozitronik\core\models\core_module\PluginsSupport;
use pozitronik\references\ReferencesModule;
use pozitronik\widgets\BadgeWidget;
use Throwable;
use Yii;
use yii\base\ErrorException;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;
use pozitronik\helpers\ArrayHelper;
use yii\helpers\Html;
use RuntimeException;

/**
 * Class Reference
 * Базовая реализация справочника
 * Справочник - стандартная шаблонная модель. Табличка обязательно имеет три поля int(id), (string)name, (bool)deleted
 * Правила и подписи стандартным полям заданы по умолчанию, при необходимссти перекрываются при наследовании.
 * В таблице могут быть отдельные поля, тогда rules() и attributeLabels) также перекрываются при наследовании.
 * Для того, чтобы имя справочника везде корректно отображалось, нужно перекрыть геттер getRef_name().
 * Для того, чтобы задать в index/view свой набор полей, можно перекрыть геттеры getColumns()/getView_columns().
 * Если у справочника своя форма редактирования (например, с дополнительными полями), возвращаем путь к этой вьюхе в getForm().
 * Если форма лежит в @app/views/admin/references/{formName()}/_form.php, то она подтянется автоматически, так что это рекомендуемое расположение вьюх.
 *
 * Получение данных из справочника для выбиралок делаем через mapData() (метод можно перекрывать по необходимости, см. Mcc)
 *
 * @package app\models\references
 *
 * @property int $usedCount Количество объектов, использующих это значение справочника
 * @property null|string $pluginId Плагин, подключающий расширение
 * @property null|CoreModule $plugin
 *
 */
class Reference extends ActiveRecord implements ReferenceInterface {
	use ARExtended;

	public $menuCaption = "Справочник";
	/*	Массив, перечисляющий имена атрибутов, которые должны отдаваться в dataOptions
		Имя может быть строковое (если название атрибута совпадает с именем data-атрибута, либо массивом
		формата ['имя data-атрибута' => 'атрибут модели']
	*/
	protected $_dataAttributes = [];
	protected $_pluginId;//deprecated

	/**
	 * @return string
	 * @throws RuntimeException
	 */
	public static function tableName():string {
		throw new RuntimeException('Забыли определить имя таблицы, вот олухи');
	}

	/**
	 * @inheritdoc
	 */
	public function rules():array {
		return [
			[['name'], 'required'],
			[['name'], 'unique'],
			[['id', 'usedCount'], 'integer'],
			[['deleted'], 'boolean'],
			[['name'], 'string', 'max' => 256],
			[['value'], 'string', 'max' => 512]
		];
	}

	/**
	 * @return LCQuery
	 */
	public static function find():LCQuery {
		return (new LCQuery(static::class))->cache(null, new TagDependency(['tags' => static::class.'::find']));

	}

	/**
	 * {@inheritDoc}
	 */
	public function afterSave($insert, $changedAttributes):void {
		parent::afterSave($insert, $changedAttributes);
		$class = static::class;
		TagDependency::invalidate(Yii::$app->cache, ["{$class}::find"]);
		TagDependency::invalidate(Yii::$app->cache, ["{$class}::MapData"]);
		TagDependency::invalidate(Yii::$app->cache, ["{$class}::DataOptions"]);
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels():array {
		return [
			'id' => 'ID',
			'name' => 'Название',
			'deleted' => 'Удалёно',
			'usedCount' => 'Использований'
		];
	}

	/**
	 * Набор колонок для отображения на главной
	 * @return array
	 */
	public function getColumns():array {
		return [
			[
				'attribute' => 'id',
				'options' => [
					'style' => 'width:36px;'
				]
			],
			[
				'attribute' => 'name',
				'value' => static function($model) {
					/** @var self $model */
					return $model->deleted?Html::tag('span', "Удалено:", [
							'class' => 'label label-danger'
						]).$model->name:BadgeWidget::widget([
						'models' => $model,
						'attribute' => 'name',
						'linkScheme' => [ReferencesModule::to(['references/update']), 'id' => 'id', 'class' => $model->formName()],
						'itemsSeparator' => false
					]);
				},
				'format' => 'raw'
			],
			[
				'attribute' => 'usedCount',
				'filter' => false,
				'value' => static function($model) {
					/** @var self $model */
					return BadgeWidget::widget([
						'models' => $model,
						'attribute' => 'usedCount',
						'linkScheme' => false,
						'itemsSeparator' => false
					]);
				},
				'format' => 'raw'
			]
		];
	}

	/**
	 * Набор колонок для отображения на странице просмотра
	 * @return array
	 */
	public function getView_columns():array {
		return $this->columns;
	}

	/**
	 * Ищет заданную вьюху сначала в каталоге вьюх класса, если там нет - вернёт дефолтную
	 * @param string $viewName
	 * @return string
	 * @throws InvalidConfigException
	 * @throws Throwable
	 */
	private function getViewPath(string $viewName):string {
		$file_path = mb_strtolower($this->formName())."/{$viewName}.php";
		if (null !== $plugin = ReferenceLoader::getReferenceByClassName($this->formName())->plugin) {//это справочник расширения
			$form_alias = $plugin->alias.'/views/references/'.$file_path;
			if (file_exists(Yii::getAlias($form_alias))) return $form_alias;

		}

		return file_exists(Yii::$app->controller->module->viewPath.DIRECTORY_SEPARATOR.Yii::$app->controller->id.DIRECTORY_SEPARATOR.$file_path)?$file_path:$viewName;
	}

	/**
	 * Если в справочнике требуется редактировать поля, кроме обязательных, то функция возвращает путь к встраиваемой вьюхе, иначе к дефолтной
	 *
	 * Сначала проверяем наличие вьюхи в расширении (/module/views/{formName}/_form.php). Если её нет, то проверяем такой же путь в модуле справочников.
	 * Если и там ничего нет, скатываемся на показ дефолтной вьюхи
	 *
	 * @return string
	 * @throws InvalidConfigException
	 * @throws Throwable
	 */
	public function getForm():string {
		return $this->getViewPath('_form');
	}

	/**
	 * @inheritDoc
	 * @return string
	 * @throws InvalidConfigException
	 * @throws Throwable
	 */
	public function getIndexForm():string {
		return $this->getViewPath('index');
	}

	/**
	 * Поиск по модели справочника
	 * @param array $params
	 * @return ActiveQuery
	 */
	public function search(array $params):ActiveQuery {
		/** @var ActiveQuery $query */
		$query = self::find();
		$this->load($params);
		$query->andFilterWhere(['LIKE', 'name', $this->name]);

		return $query;
	}

	/**
	 * @inheritdoc
	 */
	public static function mapData(bool $sort = true):array {
		return Yii::$app->cache->getOrSet(static::class."::MapData($sort)", static function() use ($sort) {
			$data = ArrayHelper::map(self::find()->active()->all(), 'id', 'name');
			if ($sort) {
				asort($data);
			}
			return $data;
		}, null, new TagDependency(['tags' => static::class."::MapData"]));
	}

	/**
	 * Объединяет две записи справочника (все ссылки на fromId ведут на toId, fromId удаляется)
	 * @param int $fromId
	 * @param int $toId
	 * @throws ErrorException
	 */
	public static function merge(int $fromId, int $toId):void {
		throw new ErrorException('Метод merge не имеет реализации по умолчанию');
	}

	/**
	 * Количество объектов, использующих это значение справочника
	 * @return int
	 */
	public function getUsedCount():int {
		return 0;
	}

	/**
	 * @return array|false
	 */
	public function getSearchSort():?array {
		$sortAttributes = [[]];
		foreach ($this->rules() as $rule) {//Сортировать по всему, что вписано в рулесы
			$sortAttributes[] = is_array($rule[0])?$rule[0]:[$rule[0]];
		}
		$sortAttributes = array_unique(array_merge(...$sortAttributes));
		return [
			'defaultOrder' => [
				'id' => SORT_ASC
			],
			'attributes' => $sortAttributes
		];
	}

	/**
	 * Возвращает набор параметров в виде data-опций, которые виджет выбиралки присунет в селект.
	 * Рекомендуемый способ получения опций через аякс не менее геморроен, но ещё и не работает
	 * @return array
	 */
	public static function dataOptions():array {
		return Yii::$app->cache->getOrSet(static::class."::DataOptions", static function() {
			/** @var self[] $items */
			$items = self::find()->active()->all();
			$result = [];
			$dataAttributes = (array)(new static)->_dataAttributes;//Получаем аттрибуты единожды, чтобы не дёргать $item->_dataAttributes в цикле
			foreach ($dataAttributes as $attribute) {
				if (is_array($attribute)) {
					$dataKey = ArrayHelper::key($attribute);
					$attributeName = $attribute[$dataKey];
				} else {
					$dataKey = $attribute;
					$attributeName = $attribute;
				}
				foreach ($items as $key => $item) {
					$result[$item->id]["data-{$dataKey}"] = $item->$attributeName;
				}

			}
			return $result;
		}, null, new TagDependency(['tags' => static::class."::DataOptions"]));
	}

	/**
	 * Возвращает имя раширения, добавившего справочник (null, если справочник базовый)
	 * @return string|null
	 */
	public function getPluginId():?string {
		return $this->_pluginId;
	}

	/**
	 * @param string|null $pluginId
	 */
	public function setPluginId(?string $pluginId):void {
		$this->_pluginId = $pluginId;
	}

	/**
	 * @return CoreModule|null
	 * @throws InvalidConfigException
	 * @throws Throwable
	 */
	public function getPlugin():?CoreModule {
		return (null === $this->pluginId)?null:PluginsSupport::GetPluginById($this->pluginId);
	}

	/**
	 * Поиск id записи по имени
	 * @param string $name
	 * @return int|null
	 */
	public static function findId(string $name):?int {
		return (null === $record = static::find()->where(['name' => $name])->one())?null:$record->id;
	}

	/**
	 * @inheritDoc
	 */
	public function getDataProvider():?DataProviderInterface {
		return null;
	}
}
