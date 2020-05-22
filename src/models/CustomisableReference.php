<?php /** @noinspection UndetectableTableInspection */
declare(strict_types = 1);

namespace pozitronik\references\models;

use pozitronik\core\models\lcquery\LCQuery;
use pozitronik\references\ReferencesModule;
use pozitronik\widgets\BadgeWidget;
use Throwable;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\Expression;
use yii\helpers\Html;

/**
 * Class CustomisableReference
 * Расширение класса справочника с поддержкой настроек отображения
 * @package app\modules\references\models
 *
 * @property string $color -- html code in rgb(r,g,b) format
 * @property string $textcolor -- css font options
 * @property-read string $style -- css style (combined font/background colors). It is preferred property, it work much faster!
 */
class CustomisableReference extends Reference {

	protected $_dataAttributes = ['color', 'textcolor'];
	protected $_usedCount;//для поиска

	/**
	 * @inheritdoc
	 */
	public function rules():array {
		return [
			[['name'], 'required'],
			[['name'], 'unique'],
			[['id', 'usedCount'], 'integer'],
			[['deleted'], 'boolean'],
			[['name', 'color', 'textcolor'], 'string', 'max' => 256]
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels():array {
		return [
			'id' => 'ID',
			'name' => 'Название',
			'deleted' => 'Удалёно',
			'usedCount' => 'Использований',
			'color' => 'Цвет фона',
			'textcolor' => 'Цвет текста'
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
						'itemsSeparator' => false,
						"optionsMap" => self::colorStyleOptions()
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
						'itemsSeparator' => false,
						"optionsMap" => static function() {
							return self::colorStyleOptions();
						}
					]);
				},
				'format' => 'raw'
			]
		];
	}

	/**
	 * {@inheritDoc}
	 */
	public function afterSave($insert, $changedAttributes) {
		parent::afterSave($insert, $changedAttributes);
		self::flushCache();
	}

	/**
	 * @inheritdoc
	 */
	public static function flushCache():void {
		$class = static::class;
		$cacheNames = [
			"{$class}MapData",
			"{$class}DataOptions",
			"{$class}ColorStyleOptions"
		];
		foreach ($cacheNames as $className) {
			Yii::$app->cache->delete($className);
		}
	}

	/**
	 * Возвращает параметр цвета (если поддерживается справочником) в виде стиля для отображения в BadgeWidget (или любом другом похожем выводе)
	 * @return array
	 */
	public static function colorStyleOptions():array {
		return Yii::$app->cache->getOrSet(static::class."ColorStyleOptions", static function() {
			$selection = self::find()->select(['id', new Expression('CONCAT ("background: " , IFNULL(color, "gray"), "; color: ", IFNULL(textcolor, "white")) AS style')])->active()->asArray()->all();
			$result = [];
			foreach ($selection as $key => $value) {
				$result[$value['id']] = [
					'style' => $value['style']
				];
			}
			return $result;
		});
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
		$file_path = mb_strtolower($this->formName()).'/_form.php';
		if (null !== $plugin = ReferenceLoader::getReferenceByClassName($this->formName())->plugin) {//это справочник расширения
			$form_alias = $plugin->alias.'/views/references/'.$file_path;
			if (file_exists(Yii::getAlias($form_alias))) return $form_alias;

		}
		$default_form = $this->hasProperty('color')?'_form_color':'_form';//аналогично родительскому вызову, но проверяем наличие вьюхи с настройками

		return file_exists(Yii::$app->controller->module->viewPath.DIRECTORY_SEPARATOR.Yii::$app->controller->id.DIRECTORY_SEPARATOR.$file_path)?$file_path:$default_form;
	}

	/**
	 * Дефолтный геттер цвета для справочников, не имплементирующих атрибут
	 * @return string|null
	 */
	public function getColor():?string {
		return null;
	}

	/**
	 * @return string
	 * @throws Throwable
	 */
	public function getFont():?string {
		return null;
	}

	/**
	 * @return string
	 */
	public function getStyle():string {
		$id = $this->id;
		return Yii::$app->cache->getOrSet(static::class."getStyle{$this->id}", static function() use ($id) {
			$styleArray = self::find()->select(new Expression('CONCAT ("background: " , IFNULL(color, "gray"), "; color: ", IFNULL(textcolor, "white")) AS style'))->asArray()->where(['id' => $id])->one();
			return $styleArray['style'];
		});
	}

	/**
	 * Фейкосеттер для реализации поиска
	 * @param int $count
	 */
	public function setUsedCount(int $count):void {
		$this->_usedCount = $count;
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
		unset($sortAttributes[array_search('usedCount', $sortAttributes)]);//сортировка по виртуальному атрибуту не нужна
		return [
			'defaultOrder' => [
				'id' => SORT_ASC
			],
			'attributes' => $sortAttributes
		];
	}
}