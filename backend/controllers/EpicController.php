<?php

namespace backend\controllers;

use common\models\UserEpic;
use Yii;
use common\models\Epic;
use common\models\EpicQuery;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * EpicController implements the CRUD actions for Epic model.
 */
class EpicController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['create', 'delete', 'index', 'update', 'view', 'participant-add'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Epic models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new EpicQuery();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Epic model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        if (empty(Yii::$app->params['activeEpic'])) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'ERROR_NO_EPIC_ACTIVE'));
        } elseif (Yii::$app->params['activeEpic']->epic_id <> $model->epic_id) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'ERROR_WRONG_EPIC'));
        }

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new Epic model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Epic();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->epic_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Epic model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->epic_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Epic model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Adds a participant
     * @param string $epic_id
     * @return mixed
     */
    public function actionParticipantAdd($epic_id)
    {
        $model = new UserEpic();

        $model->epic_id = $epic_id;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $referrer = Yii::$app->getRequest()->getReferrer();
            if ($referrer) {
                return $this->redirect(['view', 'id' => $epic_id]);
            } else {
                return $this->redirect(['index']);
            }
        } else {
            if (Yii::$app->request->isAjax) {
                return $this->renderAjax('participant/add', ['model' => $model]);
            } else {
                return $this->render('participant/add', ['model' => $model]);
            }
        }
    }

    /**
     * Finds the Epic model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Epic the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Epic::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'PAGE_NOT_FOUND'));
        }
    }
}
