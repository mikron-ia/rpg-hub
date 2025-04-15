<?php

namespace backend\controllers;

use backend\controllers\tools\EpicAssistance;
use common\models\Epic;
use common\models\PointInTime;
use common\models\PointInTimeQuery;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * PointInTimeController implements the CRUD actions for PointInTime model.
 */
class PointInTimeController extends Controller
{
    use EpicAssistance;

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['create', 'index', 'update', 'view', 'delete', 'move-up', 'move-down'],
                        'allow' => true,
                        'roles' => ['operator'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all PointInTime models.
     */
    public function actionIndex(string $epic): string
    {
        if (!empty($epic)) {
            $epicObject = $this->findEpicByKey($epic);

            if (!$epicObject->canUserViewYou()) {
                Epic::throwExceptionAboutView();
            }

            $this->selectEpic($epicObject->key, $epicObject->epic_id, $epicObject->name);
        }

        if (empty(Yii::$app->params['activeEpic'])) {
            return $this->render('../epic-list');
        }

        if (!PointInTime::canUserIndexThem()) {
            PointInTime::throwExceptionAboutIndex();
        }

        $searchModel = new PointInTimeQuery();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'epic' => $epicObject ?? Yii::$app->params['activeEpic'],
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single PointInTime model.
     */
    public function actionView(int $id): string
    {
        $model = $this->findModel($id);

        if (!$model->canUserViewYou()) {
            PointInTime::throwExceptionAboutView();
        }

        return $this->render('view', ['model' => $model]);
    }

    /**
     * Creates a new PointInTime model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionCreate(string $epic = null): Response|string
    {
        if (!PointInTime::canUserCreateThem()) {
            PointInTime::throwExceptionAboutCreate();
        }

        $model = new PointInTime();

        $this->setEpicOnObject($epic, $model);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->point_in_time_id]);
        }

        return $this->render('create', ['model' => $model]);
    }

    /**
     * Updates an existing PointInTime model.
     * If update is successful, the browser will be redirected to the 'view' page.
     */
    public function actionUpdate(int $id): Response|string
    {
        $model = $this->findModel($id);

        if (!$model->canUserControlYou()) {
            PointInTime::throwExceptionAboutControl();
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->point_in_time_id]);
        }

        return $this->render('update', ['model' => $model]);
    }

    /**
     * Deletes an existing PointInTime model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     */
    public function actionDelete(int $id): Response
    {
        $model = $this->findModel($id);

        if (!$model->canUserControlYou()) {
            PointInTime::throwExceptionAboutControl();
        }

        $model->delete();

        return $this->redirect(['index', 'epic' => $model->epic->key]);
    }

    /**
     * Moves PointInTime up in order; this means lower position on the list
     */
    public function actionMoveUp(int $id): Response
    {
        $model = $this->findModel($id);
        if (!$model->canUserControlYou()) {
            PointInTime::throwExceptionAboutControl();
        }
        $model->movePrev();

        $referrer = Yii::$app->getRequest()->getReferrer();
        if ($referrer) {
            return Yii::$app->getResponse()->redirect($referrer);
        } else {
            return $this->redirect(['index']);
        }
    }

    /**
     * Moves PointInTime down in order; this means higher position on the list
     */
    public function actionMoveDown(int $id): Response
    {
        $model = $this->findModel($id);
        if (!$model->canUserControlYou()) {
            PointInTime::throwExceptionAboutControl();
        }
        $model->moveNext();

        $referrer = Yii::$app->getRequest()->getReferrer();
        if ($referrer) {
            return Yii::$app->getResponse()->redirect($referrer);
        } else {
            return $this->redirect(['index']);
        }
    }

    /**
     * Finds the PointInTime model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     */
    protected function findModel(int $id): PointInTime
    {
        $model = PointInTime::findOne($id);

        if ($model === null) {
            throw new NotFoundHttpException(Yii::t('app', 'POINT_IN_TIME_NOT_AVAILABLE'));
        }

        $this->selectEpic($model->epic->key, $model->epic_id, $model->epic->name);

        return $model;
    }
}
