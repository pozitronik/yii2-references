<?php
declare(strict_types = 1);

namespace pozitronik\references;

use pozitronik\traits\traits\ModuleTrait;
use Yii;
use yii\base\Module;

/**
 * Class ReferencesModule
 * @package app\modules\references
 */
class ReferencesModule extends Module {
	use ModuleTrait;

	/**
	 * {@inheritDoc}
	 */
	public function getControllerPath() {
		return Yii::getAlias('@vendor/pozitronik/yii2-references/src/controllers');
	}
}
