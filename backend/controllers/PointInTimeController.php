<?php

namespace backend\controllers;

use backend\controllers\tools\EpicAssistance;
use common\models\PointInTime;
use common\models\PointInTimeQuery;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

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
     * @return mixed
     */
    public function actionIndex()
    {
        if (empty(Yii::$app->params['activeEpic'])) {
            return $this->render('../epic-selection');
        }

        if (!PointInTime::canUserIndexThem()) {
            PointInTime::throwExceptionAboutIndex();
        }

        $searchModel = new PointInTimeQuery();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single PointInTime model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        if (empty(Yii::$app->params['activeEpic'])) {
            return $this->render('../epic-selection', ['objectEpic' => $model->epic]);
        }

        if (!$model->canUserViewYou()) {
            PointInTime::throwExceptionAboutView();
        }

        if (Yii::$app->params['activeEpic']->epic_id <> $model->epic_id) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'ERROR_WRONG_EPIC'));
        }

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new PointInTime model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        if (!PointInTime::canUserCreateThem()) {
            PointInTime::throwExceptionAboutCreate();
        }

        $model = new PointInTime();

        $model->setCurrentEpicOnEmpty();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->point_in_time_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing PointInTime model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if (!$model->canUserControlYou()) {
            PointInTime::throwExceptionAboutControl();
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->point_in_time_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing PointInTime model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if (!$model->canUserControlYou()) {
            PointInTime::throwExceptionAboutControl();
        }

        $model->delete();

        return $this->redirect(['index']);
    }

    /**
     * Moves game up in order; this means lower position on the list
     * @param int $id Story ID
     * @return \yii\web\Response
     */
    public function actionMoveUp($id)
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
     * Moves game down in order; this means higher position on the list
     * @param int $id Story ID
     * @return \yii\web\Response
     */
    public function actionMoveDown($id)
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
     * @param string $id
     * @return PointInTime the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = PointInTime::findOne($id)) !== null) {
            $this->selectEpic($model->epic->key, $model->epic_id, $model->epic->name);
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'POINT_IN_TIME_NOT_AVAILABLE'));
        }
    }
}
