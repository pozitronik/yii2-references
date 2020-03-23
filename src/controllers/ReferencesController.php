<?php
declare(strict_types = 1);

namespace pozitronik\references\controllers;

use pozitronik\references\models\ReferenceLoader;
use Yii;
use yii\base\InvalidConfigException;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use Throwable;
use yii\web\Response;

/**
 * Управление всеми справочниками
 */
class ReferencesController extends Controller {
	/**
	 * @param string|null $class имя класса справочника
	 * @return mixed
	 * @throws Throwable
	 */
	public function actionIndex(?string $class = null) {
		if (null === $class) {//list all reference models
			$dataProvider = new ArrayDataProvider([
				'allModels' => ReferenceLoader::getList()
			]);
			return $this->render('list', [
				'dataProvider' => $dataProvider
			]);
		}

		if (null === $reference = ReferenceLoader::getReferenceByClassName($class)) {
			throw new InvalidConfigException("$class reference not found in configuration scope");
		}
		$dataProvider = $reference->dataProvider??new ActiveDataProvider([
			'query' => $reference->search(Yii::$app->request->queryParams),
			'sort' => $reference->searchSort
		]);

		return $this->render('index', [
			'searchModel' => $reference,
			'dataProvider' => $dataProvider,
			'class' => $reference
		]);

	}

	/**
	 * @param string $class
	 * @param integer $id
	 * @return mixed
	 * @throws Throwable
	 * @unused
	 */
	public function actionView($class, $id) {
		return $this->render('view', [
			'model' => ReferenceLoader::getReferenceByClassName($class)::findModel($id, new NotFoundHttpException())
		]);
	}

	/**
	 * @param string $class
	 * @return null|string|Response
	 * @throws Throwable
	 */
	public function actionCreate($class) {
		if (null === $model = ReferenceLoader::getReferenceByClassName($class)) return null;
		if ($model->createModel(Yii::$app->request->post($model->formName()))) {
			if (Yii::$app->request->post('more', false)) return $this->redirect(['create', 'class' => $class]);//Создали и создаём ещё
			return $this->redirect(['index', 'class' => $class]);
		}

		return $this->render('create', [
			'model' => $model
		]);
	}

	/**
	 * @param string $class
	 * @param integer $id
	 * @return null|string|Response
	 * @throws Throwable
	 */
	public function actionUpdate($class, $id) {
		if (null === $model = ReferenceLoader::getReferenceByClassName($class)::findModel($id, new NotFoundHttpException())) return null;
		if ($model->updateModel(Yii::$app->request->post($model->formName()))) {
			return $this->redirect(['update', 'id' => $model->id, 'class' => $class]);
		}

		return $this->render('update', [
			'model' => $model
		]);
	}

	/**
	 * @param string $class
	 * @param integer $id
	 * @return mixed
	 * @throws Throwable
	 */
	public function actionDelete($class, $id) {
		if (null !== $model = ReferenceLoader::getReferenceByClassName($class)::findModel($id, new NotFoundHttpException())) $model->safeDelete();
		return $this->redirect(['index', 'class' => $class]);
	}
}
