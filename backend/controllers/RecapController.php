<?php

namespace backend\controllers;

use backend\controllers\tools\EpicAssistance;
use common\models\Epic;
use common\models\Recap;
use common\models\RecapQuery;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * RecapController implements the CRUD actions for Recap model.
 */
final class RecapController extends Controller
{
    use EpicAssistance;

    private const POSITIONS_PER_PAGE = 16;

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
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
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Recap models.
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

        if (!Recap::canUserIndexThem()) {
            Recap::throwExceptionAboutIndex();
        }

        $searchModel = new RecapQuery(self::POSITIONS_PER_PAGE);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'epic' => $epicObject ?? Yii::$app->params['activeEpic'],
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Recap model.
     */
    public function actionView(string $key): string
    {
        $model = $this->findModel($key);

        if (!$model->canUserViewYou()) {
            Recap::throwExceptionAboutView();
        }

        return $this->render('view', ['model' => $model]);
    }

    /**
     * Creates a new Recap model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionCreate(string $epic = null): Response|string
    {
        if (!Recap::canUserCreateThem()) {
            Recap::throwExceptionAboutCreate();
        }

        $model = new Recap();

        $this->setEpicOnObject($epic, $model);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'key' => $model->key]);
        }

        return $this->render('create', ['model' => $model]);
    }

    /**
     * Updates an existing Recap model.
     * If update is successful, the browser will be redirected to the 'view' page.
     */
    public function actionUpdate(string $key): Response|string
    {
        $model = $this->findModel($key);

        if (!$model->canUserControlYou()) {
            Recap::throwExceptionAboutControl();
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'key' => $model->key]);
        }

        return $this->render('update', ['model' => $model]);
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
     * Finds the Recap model based on its primary key value
     */
    protected function findModel(string $key): Recap
    {
        $model = Recap::findOne(['key' => $key]);

        if ($model === null) {
            throw new NotFoundHttpException(Yii::t('app', 'RECAP_NOT_AVAILABLE'));
        }

        $this->selectEpic($model->epic->key, $model->epic_id, $model->epic->name);

        return $model;
    }
}
