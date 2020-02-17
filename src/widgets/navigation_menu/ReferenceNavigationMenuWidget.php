<?php
declare(strict_types = 1);

namespace pozitronik\references\widgets\navigation_menu;

use pozitronik\helpers\IconsHelper;
use pozitronik\references\models\Reference;
use pozitronik\references\ReferencesModule;
use pozitronik\widgets\BaseNavigationMenuWidget;
use Throwable;
use yii\base\InvalidConfigException;

/**
 * Class ReferenceNavigationMenuWidget
 * @property Reference $model
 * @property string $className
 */
class ReferenceNavigationMenuWidget extends BaseNavigationMenuWidget {
	public $className;

	/**
	 * Функция возврата результата рендеринга виджета
	 * @return string
	 * @throws InvalidConfigException
	 * @throws Throwable
	 */
	public function run():string {
		$this->_navigationItems = [
			[
				'label' => IconsHelper::view().'Просмотр',
				'url' => ReferencesModule::to(['references/view', 'id' => $this->model->id, 'class' => $this->className])
			],
			[
				'label' => IconsHelper::update().'Изменение',
				'url' => ReferencesModule::to(['references/update', 'id' => $this->model->id, 'class' => $this->className])
			],
			[
				'menu' => true,
				'label' => IconsHelper::delete().'Удаление',
				'url' => ReferencesModule::to(['references/delete', 'id' => $this->model->id, 'class' => $this->className]),
				'linkOptions' => [
					'title' => 'Удалить запись',
					'data' => [
						'confirm' => $this->model->deleted?'Вы действительно хотите восстановить запись?':'Вы действительно хотите удалить запись?',
						'method' => 'post'
					]
				]
			]
		];

		return parent::run();
	}
}
