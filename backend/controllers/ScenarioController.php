<?php

namespace backend\controllers;

use backend\controllers\tools\EpicAssistance;
use common\models\Epic;
use common\models\Scenario;
use common\models\ScenarioQuery;
use common\models\tools\ToolsForEntity;
use Throwable;
use Yii;
use yii\db\StaleObjectException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

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
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['create', 'index', 'update', 'view', 'delete'],
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

        if (!Scenario::canUserIndexThem()) {
            Scenario::throwExceptionAboutIndex();
        }

        $searchModel = new ScenarioQuery();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'epic' => $epicObject ?? Yii::$app->params['activeEpic'],
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Scenario model.
     */
    public function actionView(string $key): string
    {
        $model = $this->findModel($key);

        if (!$model->canUserViewYou()) {
            Scenario::throwExceptionAboutView();
        }

        return $this->render('view', [
            'model' => $this->findModel($key),
        ]);
    }

    /**
     * Creates a new Scenario model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionCreate(string $epic = null): Response|string
    {
        if (!Scenario::canUserCreateThem()) {
            Scenario::throwExceptionAboutCreate();
        }

        $model = new Scenario();

        $this->setEpicOnObject($epic, $model);

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
     */
    public function actionUpdate($key)
    {
        $model = $this->findModel($key);

        if (!$model->canUserControlYou()) {
            Scenario::throwExceptionAboutControl();
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'key' => $model->key]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Scenario model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @throws NotFoundHttpException
     * @throws Throwable
     * @throws StaleObjectException
     * @throws HttpException
     */
    public function actionDelete(string $key): Response
    {
        $model = $this->findModel($key);

        if (!$model->canUserControlYou()) {
            Scenario::throwExceptionAboutControl();
        }

        $model->delete();

        return $this->redirect(['index', 'epic' => $model->epic->key]);
    }

    /**
     * Finds the Scenario model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     */
    protected function findModel(string $key): Scenario
    {
        $model = Scenario::findOne(['key' => $key]);

        if ($model === null) {
            throw new NotFoundHttpException(Yii::t('app', 'SCENARIO_NOT_AVAILABLE'));
        }

        $this->selectEpic($model->epic->key, $model->epic_id, $model->epic->name);

        $this->selectEpic($model->epic->key, $model->epic_id, $model->epic->name);
        return $model;
    }
}
