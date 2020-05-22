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
 * @property ReferenceInterface $referenceClass Модель справочника, к которой интегрируется виджет
 * @property bool $showEditAddon Включает кнопку перехода к редактированию справочника
 */
class ReferenceSelectWidget extends Select2 {
	public $referenceClass;
	public $showEditAddon = true;

	/**
	 * Функция инициализации и нормализации свойств виджета
	 */
	public function init():void {
		parent::init();
		ReferenceSelectWidgetAssets::register($this->getView());
	}

	/**
	 * Функция возврата результата рендеринга виджета
	 * @return string
	 * @throws ReflectionException
	 * @throws InvalidConfigException
	 * @throws UnknownClassException
	 * @throws Throwable
	 */
	public function run():?string {
		if (true === ArrayHelper::getValue($this, 'pluginOptions.allowClear') && null === ArrayHelper::getValue($this, 'pluginOptions.placeholder')) $this->pluginOptions['placeholder'] = 'Выберите значение';

		if (null !== $this->referenceClass) {
			$this->pluginOptions['templateResult'] = new JsExpression('function(item) {return formatReferenceItem(item)}');
			$this->pluginOptions['templateSelection'] = new JsExpression('function(item) {return formatSelectedReferenceItem(item)}');
			$this->pluginOptions['escapeMarkup'] = new JsExpression('function (markup) { return markup; }');
			$this->data = $this->data??$this->referenceClass::mapData();
			$this->options['options'] = $this->referenceClass::dataOptions();
			if ($this->showEditAddon) {
				$this->addon = [
					'append' => [
						'content' => ReferencesModule::a("<i class='fa fa-edit ' title='Редактирование'></i>", ['references/index', 'class' => ReflectionHelper::GetClassShortName((string)$this->referenceClass)], ['class' => 'btn btn-default']),
						'asButton' => true
					]
				];
			}
		}
		return parent::run();
	}
}
