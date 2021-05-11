<?php
declare(strict_types = 1);

namespace pozitronik\references\widgets\navigation_menu_base;

use yii\web\AssetBundle;

/**
 * Class NavigationMenuWidgetAssets
 * @package app\components\navigation_menu
 */
class BaseNavigationMenuWidgetAssets extends AssetBundle {
	/**
	 * @inheritdoc
	 */
	public function init():void {
		$this->sourcePath = __DIR__.'/assets';
		$this->css = ['css/navigation_menu.css'];
		$this->js = ['js/navigation_menu.js'];
//		$this->publishOptions = ['forceCopy' => YII_ENV_DEV];
		parent::init();
	}
}