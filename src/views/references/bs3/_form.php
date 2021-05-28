<?php
declare(strict_types = 1);

/**
 * @var View $this
 * @var Reference $model
 * @var ActiveForm $form
 */

use pozitronik\references\models\Reference;
use yii\web\View;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<div class="panel">
	<div class="panel-heading">
		<div class="panel-control"></div>
		<h3 class="panel-title"><?= Html::encode($this->title) ?></h3>
	</div>
	<?php $form = ActiveForm::begin(); ?>
	<div class="panel-body">
		<?= $form->field($model, 'name')->textInput([
			'maxlength' => true,
			'autofocus' => 'autofocus',
			'spellcheck' => 'true'
		]) ?>
	</div>
	<div class="panel-footer">
		<?= Html::submitButton($model->isNewRecord?'Создать':'Изменить', ['class' => $model->isNewRecord?'btn btn-success':'btn btn-primary']) ?>
		<?php if ($model->isNewRecord): ?>
			<?= Html::input('submit', 'more', 'Сохранить и добавить ещё', ['class' => 'btn btn-primary']) ?>
		<?php endif ?>
	</div>
	<?php ActiveForm::end(); ?>
</div>