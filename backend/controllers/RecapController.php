<?php

namespace backend\controllers;

use Yii;
use common\models\Recap;
use common\models\RecapQuery;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * RecapController implements the CRUD actions for Recap model.
 */
final class RecapController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => [
                            'create',
                            'delete',
                            'index',
                            'update',
                            'view',
                            'move-down',
                            'move-up',
                        ],
                        'allow' => true,
                        'roles' => ['operator'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Recap models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (empty(Yii::$app->params['activeEpic'])) {
            return $this->render('../epic-selection');
        }

        if (!Recap::canUserIndexThem()) {
            Recap::throwExceptionAboutIndex();
        }

        $searchModel = new RecapQuery();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Recap model.
     * @param string $key
     * @return mixed
     */
    public function actionView($key)
    {
        $model = $this->findModel($key);

        if (empty(Yii::$app->params['activeEpic'])) {
            return $this->render('../epic-selection', ['objectEpic' => $model->epic]);
        }

        if (!$model->canUserViewYou()) {
            Recap::throwExceptionAboutView();
        }

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
     * Creates a new Recap model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        if (!Recap::canUserCreateThem()) {
            Recap::throwExceptionAboutCreate();
        }

        $model = new Recap();

        $model->setCurrentEpicOnEmpty();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'key' => $model->key]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Recap model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $key
     * @return mixed
     */
    public function actionUpdate($key)
    {
        $model = $this->findModel($key);

        if (!$model->canUserControlYou()) {
            Recap::throwExceptionAboutControl();
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->recap_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Recap model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $key
     * @return mixed
     */
    public function actionDelete($key)
    {
        $model = $this->findModel($key);

        if (!$model->canUserControlYou()) {
            Recap::throwExceptionAboutControl();
        }

        $model->delete();

        return $this->redirect(['index']);
    }

    /**
     * Moves recap up in order; this means lower position on the list
     * @param int $key Story ID
     * @return \yii\web\Response
     */
    public function actionMoveUp($key)
    {
        $model = $this->findModel($key);
        if (!$model->canUserControlYou()) {
            Recap::throwExceptionAboutControl();
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
     * Moves recap down in order; this means higher position on the list
     * @param int $key Story ID
     * @return \yii\web\Response
     */
    public function actionMoveDown($key)
    {
        $model = $this->findModel($key);
        if (!$model->canUserControlYou()) {
            Recap::throwExceptionAboutControl();
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
     * Finds the Recap model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $key
     * @return Recap the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($key)
    {
        if (($model = Recap::findOne(['key' => $key])) !== null) {
            if (empty(Yii::$app->params['activeEpic'])) {
                $this->run('site/set-epic-in-silence', ['epicKey' => $model->epic->key]);
                Yii::$app->session->setFlash('success', Yii::t('app', 'EPIC_SET_BASED_ON_OBJECT'));
            } elseif (Yii::$app->params['activeEpic']->epic_id <> $model->epic_id) {
                $this->run('site/set-epic-in-silence', ['epicKey' => $model->epic->key]);
                Yii::$app->session->setFlash('success', Yii::t('app', 'EPIC_CHANGED_BASED_ON_OBJECT'));
            }
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'RECAP_NOT_AVAILABLE'));
        }
    }
}
