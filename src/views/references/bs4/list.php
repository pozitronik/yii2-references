<?php
declare(strict_types = 1);

use kartik\grid\GridView;
use yii\bootstrap4\Html;
use yii\data\ArrayDataProvider;
use yii\web\View;
use pozitronik\references\models\Reference;

/**
 * @var View $this
 * @var ArrayDataProvider $dataProvider
 */

$this->title = 'Справочники';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="panel">
	<div class="panel-hdr">
		<h2><?= Html::encode($this->title) ?></h2>
	</div>
	<div class="panel-container show">
		<div class="panel-content">
			<?= GridView::widget([
				'dataProvider' => $dataProvider,
				'columns' => [
					[
						'attribute' => 'menuCaption',
						'label' => 'Название справочника',
						'value' => static function(Reference $referenceModel) {
							return Html::a($referenceModel->menuCaption, ['index', 'class' => $referenceModel->formName()]);
						},
						'format' => 'raw'
					],
					[
						'label' => 'Модуль',
						'value' => static function(Reference $referenceModel) {
							return $referenceModel->moduleId??'Базовый';
						}
					]
				]
			]) ?>
		</div>
	</div>
</div>