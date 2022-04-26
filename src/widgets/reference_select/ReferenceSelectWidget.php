<?php
declare(strict_types = 1);

namespace pozitronik\references\widgets\reference_select;

use pozitronik\helpers\ArrayHelper;
use pozitronik\references\models\ReferenceInterface;
use pozitronik\references\ReferencesModule;
use kartik\select2\Select2;
use pozitronik\helpers\ReflectionHelper;
use ReflectionException;
use Throwable;
use yii\base\InvalidConfigException;
use yii\base\UnknownClassException;
use yii\web\JsExpression;

/**
 * Виджет-выбиралка для любых справочников. Добавляет к Select2 стандартное для справочников форматирование данных.
 *
 * @property string $referenceClass Модель справочника, к которой интегрируется виджет
 * @property null|bool|callable $showEditAddon Включает кнопку перехода к редактированию справочника.
 * null - взять из параметра конфигурации modules.references.select_widget.showEditAddon
 * Если в параметр передано замыкание, то используется результат выполнения этого замыкания
 */
class ReferenceSelectWidget extends Select2 {
	public ?string $referenceClass;
	/**
	 * @var null|bool|callable
	 */
	public $showEditAddon;
	private ?ReferenceInterface $_referenceModel = null;

	/**
	 * Функция инициализации и нормализации свойств виджета
	 */
	public function init():void {
		parent::init();
		ReferenceSelectWidgetAssets::register($this->getView());
		$this->_referenceModel = new $this->referenceClass();
	}

	/**
	 * Функция возврата результата рендеринга виджета
	 * @return string|void
	 * @throws InvalidConfigException
	 * @throws ReflectionException
	 * @throws Throwable
	 * @throws UnknownClassException
	 */
	public function run() {
		if (true === ArrayHelper::getValue($this, 'pluginOptions.allowClear') && null === ArrayHelper::getValue($this, 'pluginOptions.placeholder')) $this->pluginOptions['placeholder'] = 'Выберите значение';

		if (null !== $this->referenceClass) {
			$this->pluginOptions['templateResult'] = new JsExpression('function(item) {return formatReferenceItem(item)}');
			$this->pluginOptions['templateSelection'] = new JsExpression('function(item) {return formatSelectedReferenceItem(item)}');
			$this->pluginOptions['escapeMarkup'] = new JsExpression('function (markup) { return markup; }');
			$this->data = $this->data??$this->_referenceModel::mapData();
			$this->options['options'] = $this->_referenceModel::dataOptions();
			if ($this->getShowEditAddon()) {
				$this->addon = [
					'append' => [
						'content' => ReferencesModule::a($this->isBs(4)
							?"<i class='fa fa-edit' title='Редактирование'></i>"
							:"<i class='glyphicon glyphicon-wrench' title='Редактирование'></i>",
							['references/index', 'class' => ReflectionHelper::GetClassShortName($this->referenceClass)], ['class' => 'btn btn-default']),
						'asButton' => true
					]
				];
			}
		}
		return parent::run();
	}

	/**
	 * @return bool
	 * @throws InvalidConfigException
	 * @throws Throwable
	 */
	private function getShowEditAddon():bool {
		$this->showEditAddon ??= ReferencesModule::param('select_widget.showEditAddon', true);
		return (is_callable($this->showEditAddon))
			?call_user_func($this->showEditAddon)
			:$this->showEditAddon;
	}
}
