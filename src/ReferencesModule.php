<?php
declare(strict_types = 1);

namespace pozitronik\references;

use pozitronik\core\models\core_module\CoreModule;
use Yii;

/**
 * Class ReferencesModule
 * @package app\modules\references
 */
class ReferencesModule extends CoreModule {

	/**
	 * {@inheritDoc}
	 */
	public function getControllerPath() {
		return Yii::getAlias('@vendor/pozitronik/yii2-references/src/controllers');
	}
}
