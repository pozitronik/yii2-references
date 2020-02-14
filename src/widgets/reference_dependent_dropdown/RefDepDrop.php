<?php
declare(strict_types = 1);

namespace pozitronik\references\widgets\reference_dependent_dropdown;

use pozitronik\helpers\ArrayHelper;
use pozitronik\references\widgets\reference_select\ReferenceSelectWidget;
use kartik\base\Config;
use kartik\depdrop\DepDrop;
use kartik\depdrop\DepDropAsset;
use kartik\depdrop\DepDropExtAsset;
use kartik\select2\Select2;
use yii\base\InvalidConfigException;

/**
 * Расширяем картиковский Dependent Dropdown для поддержки ReferenceSelectWidget
 * Class RefDepDrop
 * @package app\modules\references\widgets\referense_depedent_dropdown
 */
class RefDepDrop extends DepDrop {

	public const TYPE_REFERENCE_SELECT = 3;
	public $referenceClass;
	public $type = self::TYPE_REFERENCE_SELECT;

	/**
	 * {@inheritDoc}
	 */
	public function run() {
		if (empty($this->pluginOptions['url'])) {
			throw new InvalidConfigException("The 'pluginOptions[\"url\"]' property has not been set.");
		}
		if (empty($this->pluginOptions['depends']) || !is_array($this->pluginOptions['depends'])) {
			throw new InvalidConfigException("The 'pluginOptions[\"depends\"]' property must be set and must be an array of dependent dropdown element identifiers.");
		}
		if (empty($this->options['class'])) {
			$this->options['class'] = 'form-control';
		}
		if (in_array($this->type, [self::TYPE_SELECT2, self::TYPE_REFERENCE_SELECT])) {
			Config::checkDependency('select2\Select2', 'yii2-widget-select2', 'for dependent dropdown for Select2');
		}
		if (!in_array($this->type, [self::TYPE_SELECT2, self::TYPE_REFERENCE_SELECT]) && !empty($this->options['placeholder'])) {
			$this->data = ['' => $this->options['placeholder']] + $this->data;
		}
		if (self::TYPE_REFERENCE_SELECT === $this->type && null === $this->referenceClass) {
			throw new InvalidConfigException("The 'referenceClass' property has not been set.");
		}
		$this->registerAssets();
	}

	/**
	 * {@inheritDoc}
	 */
	public function registerAssets():void {
		$view = $this->getView();
		DepDropAsset::register($view)->addLanguage($this->language, 'depdrop_locale_');
		DepDropExtAsset::register($view);
		$this->registerPlugin($this->pluginName);
		if (in_array($this->type, [self::TYPE_SELECT2, self::TYPE_REFERENCE_SELECT])) {
			$loading = ArrayHelper::getValue($this->pluginOptions, 'loadingText', 'Loading ...');
			$this->select2Options['data'] = $this->data;
			$this->select2Options['options'] = $this->options;
			if ($this->hasModel()) {
				$settings = ArrayHelper::merge($this->select2Options, [
					'model' => $this->model,
					'attribute' => $this->attribute
				]);
			} else {
				$settings = ArrayHelper::merge($this->select2Options, [
					'name' => $this->name,
					'value' => $this->value
				]);
			}
			if (self::TYPE_SELECT2 === $this->type) {
				echo Select2::widget($settings);
			} else {//reference dropdown
				$settings['referenceClass'] = $this->referenceClass;
				echo ReferenceSelectWidget::widget($settings);
			}
			$id = $this->options['id'];
			$view->registerJs("initDepdropS2('{$id}','{$loading}');");
		} else {
			echo $this->getInput('dropdownList', true);
		}
	}
}