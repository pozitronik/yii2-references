<?php
declare(strict_types = 1);

/**
 * @var View $this
 * @var array $items
 */

use pozitronik\widgets\ColumnMenuWidgetAssets;
use yii\bootstrap\ButtonDropdown;
use yii\web\View;

/*
 * Не получается подписаться на события *.bs.dropdown, т.к. тут они триггерятся для контейнера. Возможно, я чего-то не понял сам, но проще оказалось вынести подписку на события в отдельный JS
 * Скрипт пытается позиционировать всплывающее меню так, чтобы оно не "тонуло" в таблице.
 */
ColumnMenuWidgetAssets::register($this);
?>

<?= ButtonDropdown::widget([
	'label' => "<i class='fa fa-bars'></i>",
	'encodeLabel' => false,
	'containerOptions' => [
		'class' => 'dropdown'
	],
	'dropdown' => [
		'options' => [
			'class' => 'pull-left'
		],
		'encodeLabels' => false,
		'items' => $items
	]
]) ?>

