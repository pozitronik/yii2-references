<?php
declare(strict_types = 1);

namespace pozitronik\references\models;

use pozitronik\core\helpers\ModuleHelper;
use pozitronik\helpers\ArrayHelper;
use pozitronik\widgets\BadgeWidget;
use Throwable;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\base\Module;
use yii\data\ArrayDataProvider;
use yii\data\DataProviderInterface;
use yii\db\ActiveQuery;

/**
 * Class ArrayReference
 * Read-only справочник, определяемый на уровне кода
 *
 * @property array $items Массив данных справочника в формате id => ['поле' => 'Значение']
 * @property-read int $id
 * @property-read string $name
 * @property-read bool $deleted
 */
class ArrayReference extends Model implements ReferenceInterface {
	/**
	 * @var array[]
	 */
	public array $items = [];

	public ?int $id = null;
	public ?string $name = null;
	public bool $deleted = false;

	protected $_moduleId;

	/**
	 * @param int $index
	 * @return ArrayReference
	 * @throws Throwable
	 */
	public static function loadModel(int $index):ArrayReference {
		$model = new static(['id' => $index]);
		$model->load((array)ArrayHelper::getValue($model->items, $index, []), '');
		return $model;
	}

	/**
	 * @inheritdoc
	 */
	public function rules():array {
		return [
			[['name'], 'required'],
			[['id'], 'integer'],
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

	public function getView_columns():array {
		return $this->columns;
	}

	public function getForm():?string {
		return null;
	}

	public function getIndexForm():?string {
		return null;
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

	public function search(array $params):?ActiveQuery {
		return null;
	}

	public function getSearchSort():?array {
		return null;
	}

	public static function merge(int $fromId, int $toId):void {
	}

	public function getUsedCount():?int {
		return null;
	}

	public static function dataOptions():array {
		return [];
	}

	public function getDataProvider():?DataProviderInterface {
		return new ArrayDataProvider([
			'allModels' => $this->items
		]);
	}

}