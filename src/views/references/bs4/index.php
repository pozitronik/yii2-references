<?php
declare(strict_types = 1);

use pozitronik\references\ReferencesModule;
use kartik\grid\GridView;
use yii\grid\ActionColumn;
use yii\bootstrap4\Html;
use yii\data\ActiveDataProvider;
use yii\web\View;
use pozitronik\references\models\Reference;
use yii\bootstrap4\LinkPager;

/**
 * @var View $this
 * @var ActiveDataProvider $dataProvider
 * @var Reference|false $class
 * @var Reference $searchModel
 */

$this->title = $class->menuCaption;
$this->params['breadcrumbs'][] = ReferencesModule::breadcrumbItem('Справочники');
$this->params['breadcrumbs'][] = $this->title;

$columns[] = [
	'class' => ActionColumn::class,
	'template' => '{edit}{view}{delete}',
	'buttons' => [
		'edit' => static function(string $url, Reference $model) use ($class) {
			return Html::a('<i class="fa fa-edit"></i>', ReferencesModule::to(['references/update', 'id' => $model->id, 'class' => $class->formName()]));
		},
		'view' => static function(string $url, Reference $model) use ($class) {
			return Html::a('<i class="fa fa-eye"></i>', ReferencesModule::to(['references/view', 'id' => $model->id, 'class' => $class->formName()]));
		},
		'delete' => static function(string $url, Reference $model) use ($class) {
			return Html::a('<i class="fa fa-trash"></i>', ReferencesModule::to(['references/delete', 'id' => $model->id, 'class' => $class->formName()]));
		},
	],
];

$columns = array_merge($columns, $class->columns);

?>
<div class="panel">
	<div class="panel-hdr">
		<h2><?= Html::encode($this->title) ?></h2>
		<div class="panel-toolbar">
			<?= Html::a('Создать запись', ['create', 'class' => $class->formName()], ['class' => 'btn btn-success']) ?>
		</div>
	</div>
	<div class="panel-container show">
		<div class="panel-content">
			<?= GridView::widget([
				'filterModel' => $searchModel,
				'dataProvider' => $dataProvider,
				'pager' => [
					'class' => LinkPager::class
				],
				'columns' => $columns,
				'rowOptions' => static function($record) {
					$class = '';
					if ($record['deleted']) {
						$class .= 'danger ';
					}
					return ['class' => $class];
				}
			]) ?>
		</div>
	</div>
</div>