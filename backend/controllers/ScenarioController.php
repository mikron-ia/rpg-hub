<?php

namespace backend\controllers;

use backend\controllers\tools\EpicAssistance;
use common\models\ScenarioQuery;
use common\models\tools\ToolsForEntity;
use Yii;
use common\models\Scenario;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ScenarioController implements the CRUD actions for Scenario model.
 */
class ScenarioController extends Controller
{
    use EpicAssistance;
    use ToolsForEntity;

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['create', 'index', 'update', 'view', 'delete'],
                        'allow' => true,
                        'roles' => ['operator'],
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

    public function actionIndex()
    {
        if (empty(Yii::$app->params['activeEpic'])) {
            return $this->render('../epic-selection');
        }

        if (!Scenario::canUserIndexThem()) {
            Scenario::throwExceptionAboutIndex();
        }

        $searchModel = new ScenarioQuery();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Scenario model.
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
            Scenario::throwExceptionAboutView();
        }

        if (empty(Yii::$app->params['activeEpic'])) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'ERROR_NO_EPIC_ACTIVE'));
        } elseif (Yii::$app->params['activeEpic']->epic_id <> $model->epic_id) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'ERROR_WRONG_EPIC'));
        }

        return $this->render('view', [
            'model' => $this->findModel($key),
        ]);
    }

    /**
     * Creates a new Scenario model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        if (!Scenario::canUserCreateThem()) {
            Scenario::throwExceptionAboutCreate();
        }

        $model = new Scenario();

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
     * Updates an existing Scenario model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $key
     * @return mixed
     */
    public function actionUpdate($key)
    {
        $model = $this->findModel($key);

        if (!$model->canUserControlYou()) {
            Scenario::throwExceptionAboutControl();
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->scenario_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Scenario model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $key
     * @return mixed
     */
    public function actionDelete($key)
    {
        $model = $this->findModel($key);

        if (!$model->canUserControlYou()) {
            Scenario::throwExceptionAboutControl();
        }

        $model->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Scenario model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $key
     * @return Scenario the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($key)
    {
        if (($model = Scenario::findOne(['key' => $key])) !== null) {
            $this->selectEpic($model->epic->key, $model->epic_id, $model->epic->name);
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'SCENARIO_NOT_AVAILABLE'));
        }
    }
}
