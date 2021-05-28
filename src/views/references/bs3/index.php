<?php
declare(strict_types = 1);

use pozitronik\references\ReferencesModule;
use kartik\grid\GridView;
use yii\grid\ActionColumn;
use yii\helpers\Html;
use yii\data\ActiveDataProvider;
use yii\web\View;
use pozitronik\references\models\Reference;

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
			return Html::a('<i class="glyphicon glyphicon-edit"></i>', ReferencesModule::to(['references/update', 'id' => $model->id, 'class' => $class->formName()]));
		},
		'view' => static function(string $url, Reference $model) use ($class) {
			return Html::a('<i class="glyphicon glyphicon-eye-open"></i>', ReferencesModule::to(['references/view', 'id' => $model->id, 'class' => $class->formName()]));
		},
		'delete' => static function(string $url, Reference $model) use ($class) {
			return Html::a('<i class="glyphicon glyphicon-trash"></i>', ReferencesModule::to(['references/delete', 'id' => $model->id, 'class' => $class->formName()]));
		},
	],
];

$columns = array_merge($columns, $class->columns);

?>
<div class="panel">
	<div class="panel-heading">
		<div class="panel-control">
			<?= Html::a('Создать запись', ['create', 'class' => $class->formName()], ['class' => 'btn btn-success']) ?>
		</div>
		<h3 class="panel-title"><?= Html::encode($this->title) ?></h3>
	</div>
	<div class="panel-body">
		<?= GridView::widget([
			'filterModel' => $searchModel,
			'dataProvider' => $dataProvider,
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