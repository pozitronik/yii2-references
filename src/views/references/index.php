<?php
declare(strict_types = 1);

use pozitronik\helpers\IconsHelper;
use pozitronik\references\ReferencesModule;
use kartik\grid\GridView;
use pozitronik\references\widgets\navigation_menu\ReferenceNavigationMenuWidget;
use yii\helpers\Html;
use yii\data\ActiveDataProvider;
use yii\web\View;
use pozitronik\references\models\Reference;

/**
 * @var View $this ;
 * @var ActiveDataProvider $dataProvider
 * @var Reference|false $class
 * @var Reference $searchModel
 */

$this->title = $class->menuCaption;
$this->params['breadcrumbs'][] = ReferencesModule::breadcrumbItem('Справочники');
$this->params['breadcrumbs'][] = $this->title;

$columns[] = [
	'filter' => false,
	'header' => IconsHelper::menu(),
	'mergeHeader' => true,
	'headerOptions' => [
		'class' => 'skip-export kv-align-center kv-align-middle'
	],
	'contentOptions' => [
		'style' => 'width:50px',
		'class' => 'skip-export kv-align-center kv-align-middle'
	],
	'value' => static function(Reference $model) use ($class) {
		return ReferenceNavigationMenuWidget::widget([
			'model' => $model,
			'className' => $class->formName(),
			'mode' => ReferenceNavigationMenuWidget::MODE_ACTION_COLUMN_MENU
		]);
	},
	'format' => 'raw'
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