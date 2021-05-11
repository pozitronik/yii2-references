<?php
declare(strict_types = 1);

namespace pozitronik\references\widgets\navigation_menu_base;

use pozitronik\helpers\ArrayHelper;
use pozitronik\helpers\ReflectionHelper;
use pozitronik\widgets\CachedWidget;
use yii\db\ActiveRecord;

/**
 * Class NavigationMenuWidget
 * @property ActiveRecord $model
 * @property int $mode
 *
 * Брать его текущую абсолютную позицию, менять position на fixed, возвращать позицию
 */
class BaseNavigationMenuWidget extends CachedWidget {
	public const MODE_MENU = 0;
	public const MODE_TABS = 1;
	public const MODE_BOTH = 2;//Будут отрендерены вкладки, элементы, помеченные, как menu=>true будут отрендерены в меню
	public const MODE_ACTION_COLUMN_MENU = 3;//Меню в колонке GridView

	public $model;
	public $mode = self::MODE_BOTH;

	protected $_navigationItems = [];

	/**
	 * Функция инициализации и нормализации свойств виджета
	 */
	public function init():void {
		parent::init();
		BaseNavigationMenuWidgetAssets::register($this->getView());
	}

	/**
	 * {@inheritDoc}
	 * Перекрываем getViewPath, чтобы путь к вьюхам возвращался для ЭТОГО виджета, а не для наследующей модели
	 */
	public function getViewPath():string {
		/** @noinspection NullPointerExceptionInspection */
		return dirname(ReflectionHelper::New(self::class)->getFileName()).DIRECTORY_SEPARATOR.'views';
	}

	/**
	 * Функция возврата результата рендеринга виджета
	 * @return string|array
	 */
	public function run():string {
		if ($this->model->isNewRecord) return '';
		switch ($this->mode) {
			case self::MODE_MENU:
				return $this->render('navigation_menu', [
					'items' => $this->_navigationItems
				]);
			case self::MODE_TABS:
				return $this->render('navigation_tabs', [
					'items' => $this->_navigationItems
				]);
			default:
			case self::MODE_BOTH:
				$menuItems = array_filter($this->_navigationItems, static function($element) {
					return true === ArrayHelper::getValue($element, 'menu');
				});
				$tabItems = array_diff_key($this->_navigationItems, $menuItems);

				return (([] === $tabItems)?'':$this->render('navigation_tabs', [
						'items' => $tabItems
					])).(([] === $menuItems)?'':$this->render('navigation_menu', [
						'items' => $menuItems
					]));
			case self::MODE_ACTION_COLUMN_MENU:
				return $this->render('navigation_column_menu', [
					'items' => $this->_navigationItems
				]);
		}

	}
}
