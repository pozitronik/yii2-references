<?php /** @noinspection PhpUndefinedNamespaceInspection */
declare(strict_types = 1);

/**
 * @var View $this
 * @var Reference $model
 * @var ActiveForm $form
 */

use pozitronik\references\models\Reference;
use yii\web\View;
use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;

?>

<div class="panel">

	<div class="panel-hdr">
		<h2><?= Html::encode($this->title) ?></h2>
		<div class="panel-toolbar"></div>
	</div>
	<?php $form = ActiveForm::begin(); ?>
	<div class="panel-container show">
		<div class="panel-content">
			<?= $form->field($model, 'name')->textInput([
				'maxlength' => true,
				'autofocus' => 'autofocus',
				'spellcheck' => 'true'
			]) ?>
		</div>
		<div class="panel-content">
			<?= Html::submitButton($model->isNewRecord?'Создать':'Изменить', ['class' => $model->isNewRecord?'btn btn-success':'btn btn-primary']) ?>
			<?php if ($model->isNewRecord): ?>
				<?= Html::submitButton('Сохранить и добавить ещё', ['class' => 'btn btn-primary', 'data-method' => 'POST', 'data-params' => ['more' => true]]) ?>
			<?php endif ?>
		</div>
	</div>
	<?php ActiveForm::end(); ?>
</div>