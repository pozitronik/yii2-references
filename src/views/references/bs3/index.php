<?php
declare(strict_types = 1);

use pozitronik\references\models\ArrayReference;
use pozitronik\references\models\ReferenceInterface;
use pozitronik\references\ReferencesModule;
use kartik\grid\GridView;
use yii\data\DataProviderInterface;
use yii\grid\ActionColumn;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var DataProviderInterface $dataProvider
 * @var ReferenceInterface $class
 * @var ReferenceInterface $searchModel
 */

$this->title = $class->menuCaption;
$this->params['breadcrumbs'][] = ReferencesModule::breadcrumbItem('Справочники');
$this->params['breadcrumbs'][] = $this->title;

$columns[] = [
	'class' => ActionColumn::class,
	'template' => '{edit}{view}{delete}',
	'buttons' => [
		'edit' => static fn(string $url, ReferenceInterface $model):?string => is_a($model, ArrayReference::class)?null:Html::a('<i class="glyphicon glyphicon-edit"></i>', ReferencesModule::to(['references/update', 'id' => $model->id, 'class' => $class->formName()])),
		'view' => static fn(string $url, ReferenceInterface $model):string => Html::a('<i class="glyphicon glyphicon-eye-open"></i>', ReferencesModule::to(['references/view', 'id' => $model->id, 'class' => $class->formName()])),
		'delete' => static fn(string $url, ReferenceInterface $model):?string => is_a($model, ArrayReference::class)?null:Html::a('<i class="glyphicon glyphicon-trash"></i>', ReferencesModule::to(['references/delete', 'id' => $model->id, 'class' => $class->formName()])),
	],
];

$columns = array_merge($columns, $class->columns);

?>
<div class="panel">
	<div class="panel-heading">
		<?php if (!is_a($class, ArrayReference::class)): ?>
			<div class="panel-control">
				<?= Html::a('Создать запись', ['create', 'class' => $class->formName()], ['class' => 'btn btn-success']) ?>
			</div>
		<?php endif; ?>
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