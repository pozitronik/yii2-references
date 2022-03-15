<?php
declare(strict_types = 1);

namespace pozitronik\references\models;

use pozitronik\helpers\ModuleHelper;
use pozitronik\helpers\ArrayHelper;
use pozitronik\traits\traits\ModuleTrait;
use pozitronik\widgets\BadgeWidget;
use Throwable;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\base\Module;
use yii\data\ArrayDataProvider;
use yii\data\DataProviderInterface;

/**
 * Class ArrayReference
 * Read-only справочник, определяемый на уровне кода
 *
 * @property array $items Массив данных справочника в формате id => ['поле' => 'Значение']
 * @property-read string|int $id
 * @property-read string $name
 * @property-read bool $deleted
 *
 * @property-read ArrayReference[] $models Массив моделей справочника, загруженный из конфига
 * @property null|Module $module
 */
class ArrayReference extends Model implements ReferenceInterface {

	public string $menuCaption = "Конфигурация";
	/**
	 * @var array[]
	 */
	public array $items = [];

	public string|int|null $id = null;
	public ?string $name = null;
	public bool $deleted = false;

    protected ?string $_moduleId = null;
	private array $_models = [];

	/**
	 * @inheritDoc
	 */
	public static function getRecord(string|int $id):?self {
		$model = new static(['id' => $id]);
		if (null === $data = ArrayHelper::getValue($model->items, $id)) return null;
		$model->load((array)$data, '');
		return $model;
	}

	/**
	 * @return self[]
	 */
	public function getModels():array {
		if ([] === $this->_models) {
			foreach ($this->items as $index => $item) {
				$this->_models[] = new static(['id' => $index] + $item);
			}
		}
		return $this->_models;
	}

	/**
	 * @inheritdoc
	 */
	public function rules():array {
		return [
			[['name'], 'required'],
			[['id'], 'safe'],
			[['deleted'], 'boolean'],
			[['name'], 'string', 'max' => 256]
		];
	}

	/**
	 * @inheritDoc
	 */
	public static function mapData(bool $sort = false):array {
		$data = [];
		$items = (new static())->items;
		foreach ($items as $key => $value) $data[$key] = ArrayHelper::getValue($value, 'name');
		if ($sort) {
			asort($data);
		}
		return $data;
	}

	/**
	 * @inheritDoc
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
				'value' => static function(Model $model) {
					return BadgeWidget::widget([
						'items' => $model,
						'subItem' => 'name',
					]);
				},
				'format' => 'raw'
			],
			[
				'attribute' => 'usedCount',
				'filter' => false,
				'value' => static function(Model $model) {
					return BadgeWidget::widget([
						'items' => $model,
						'subItem' => 'usedCount',
						'urlScheme' => false
					]);
				},
				'format' => 'raw'
			]
		];
	}

	/**
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
	protected function getViewPath(string $viewName):string {
		$file_path = mb_strtolower($this->formName())."/{$viewName}.php";
		/** @var ModuleTrait $module */
		if (null !== $module = ReferenceLoader::getReferenceByClassName($this->formName())->module) {//это справочник расширения
			$form_alias = $module->alias.'/views/references/'.$file_path;
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
	 * Возвращает имя расширения, добавившего справочник (null, если справочник базовый)
	 * @return string|null
	 */
	public function getModuleId():?string {
		return $this->_moduleId;
	}

	/**
	 * @param string|null $moduleId
	 */
	public function setModuleId(?string $moduleId):void {
		$this->_moduleId = $moduleId;
	}

	/**
	 * @return Module|null
	 * @throws InvalidConfigException
	 * @throws Throwable
	 */
	public function getModule():?Module {
		return (null === $this->moduleId)?null:ModuleHelper::GetModuleById($this->moduleId);
	}

	/**
	 * @inheritDoc
	 * @return null|self[]
	 */
	public function search(array $params):?array {
		return null;
	}

	/**
	 * @inheritDoc
	 */
	public function getSearchSort():?array {
		return null;
	}

	/**
	 * @inheritDoc
	 */
	public static function merge(int $fromId, int $toId):void {
	}

	/**
	 * @inheritDoc
	 */
	public function getUsedCount():?int {
		return null;
	}

	/**
	 * @inheritDoc
	 */
	public static function dataOptions():array {
		return [];
	}

	/**
	 * @inheritDoc
	 */
	public function getDataProvider():?DataProviderInterface {
		return new ArrayDataProvider([
			'allModels' => $this->models
		]);
	}

	/**
	 * @inheritDoc
	 */
	public function createRecord(?array $data):?bool {
		return null;
	}

	/**
	 * @inheritDoc
	 */
	public function updateRecord(?array $data):?bool {
		return null;
	}

	/**
	 * @inheritDoc
	 */
	public function deleteRecord():?bool {
		return null;
	}
}