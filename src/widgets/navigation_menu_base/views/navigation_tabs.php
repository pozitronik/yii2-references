<?php
declare(strict_types = 1);

/**
 * @var View $this
 * @var array $items
 */

use pozitronik\helpers\ArrayHelper;
use pozitronik\helpers\Utils;
use yii\helpers\Html;
use yii\web\View;

?>

<ul class="nav nav-tabs">
	<?php foreach ($items as $item): ?>
		<?php if (true === ArrayHelper::getValue($item, 'visible', true)): ?>
			<?php $sameUrl = Utils::isSameUrlPath($item['url']); ?>
			<?= Html::tag('li', Html::a($item['label'], $sameUrl?'#':$item['url'], ArrayHelper::getValue($item, 'linkOptions', [])), ['class' => $sameUrl?'active':'inactive']) ?>
		<?php endif; ?>
	<?php endforeach; ?>
</ul>


