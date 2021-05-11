<?php
declare(strict_types = 1);

/**
 * @var View $this
 * @var array $items
 */

use yii\bootstrap\ButtonDropdown;
use yii\web\View;

?>

<?= ButtonDropdown::widget([
	'label' => "<i class='fa fa-bars'></i>",
	'encodeLabel' => false,
	'dropdown' => [
		'options' => [
			'class' => 'pull-right'
		],
		'encodeLabels' => false,
		'items' => $items
	]
]) ?>

