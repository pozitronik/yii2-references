<?php
declare(strict_types = 1);

namespace pozitronik\references;

use pozitronik\core\traits\ModuleExtended;
use Yii;
use yii\base\Module;

/**
 * Class ReferencesModule
 * @package app\modules\references
 */
class ReferencesModule extends Module {
	use ModuleExtended;

	/**
	 * {@inheritDoc}
	 */
	public function getControllerPath() {
		return Yii::getAlias('@vendor/pozitronik/yii2-references/src/controllers');
	}
}
