<?php
/** @noinspection PhpUndefinedClassInspection */
/** @noinspection PhpUndefinedNamespaceInspection */
declare(strict_types = 1);

/**
 * @var View $this
 * @var ActiveDataProvider $dataProvider
 * @var Reference|false $class
 * @var Reference $model
 */

use pozitronik\references\ReferencesModule;
use yii\data\ActiveDataProvider;
use yii\web\View;
use yii\bootstrap4\Html;
use yii\widgets\DetailView;
use pozitronik\references\models\Reference;

$this->title = "Просмотр записи в справочнике ".$model->menuCaption;
$this->params['breadcrumbs'][] = ReferencesModule::breadcrumbItem('Справочники');
$this->params['breadcrumbs'][] = ReferencesModule::breadcrumbItem($model->menuCaption, ['references/index', 'class' => $model->formName()]);

$this->params['breadcrumbs'][] = $this->title;
?>
<div class="panel">
	<div class="panel-hdr">
		<h2><?= Html::encode($this->title) ?></h2>
		<div class="panel-toolbar">
			<div class="btn-group">
				<?= Html::a(
					'Изменить',
					['update', 'id' => $model->id, 'class' => $model->formName()],
					['class' => 'btn btn-primary']
				) ?>
				<?= Html::a(
					'Удалить',
					['delete', 'id' => $model->id, 'class' => $model->formName()],
					[
						'class' => 'btn btn-danger',
						'data' => [
							'confirm' => 'Вы действительно хотите удалить эту запись?',
							'method' => 'post'
						]
					]
				) ?>
			</div>
		</div>
	</div>
	<div class="panel-container show">
		<div class="panel-content">
			<?= DetailView::widget([
				'model' => $model,
				'attributes' => $model->view_columns
			]) ?>
		</div>
	</div>
</div>