<?php
declare(strict_types = 1);

use pozitronik\core\models\core_module\PluginsSupport;
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\data\ArrayDataProvider;
use yii\web\View;
use pozitronik\references\models\Reference;

/**
 * @var View $this ;
 * @var ArrayDataProvider $dataProvider
 */

$this->title = 'Справочники';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="panel">
	<div class="panel-heading">
		<h3 class="panel-title"><?= Html::encode($this->title) ?></h3>
	</div>
	<div class="panel-body">
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
						return null !== $referenceModel->pluginId?PluginsSupport::GetName($referenceModel->pluginId):'Базовый';
					}
				]
			]
		]) ?>
	</div>
</div>