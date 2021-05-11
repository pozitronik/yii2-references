<?php
declare(strict_types = 1);

namespace pozitronik\references\widgets\navigation_menu_base;

use yii\web\AssetBundle;

/**
 * Class NavigationMenuWidgetAssets
 * @package app\components\navigation_menu
 */
class ColumnMenuWidgetAssets extends AssetBundle {
	/**
	 * @inheritdoc
	 */
	public function init():void {
		$this->sourcePath = __DIR__.'/assets';
		$this->js = ['js/navigation_column_menu.js'];
//		$this->publishOptions = ['forceCopy' => YII_ENV_DEV];
		parent::init();
	}
}