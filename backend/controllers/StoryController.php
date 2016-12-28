<?php

namespace backend\controllers;

use common\models\StoryQuery;
use Yii;
use common\models\Story;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * StoryController implements the CRUD actions for Story model.
 */
final class StoryController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => [
                            'index',
                            'create',
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
     * Lists all stories
     * @return mixed
     */
    public function actionIndex()
    {
        if(!Story::canUserIndexThem()) {
            Story::throwExceptionAboutIndex();
        }

        $searchModel = new StoryQuery(16);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single story
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        if(!$model->canUserViewYou()) {
            Story::throwExceptionAboutView();
        }

        if (empty(Yii::$app->params['activeEpic'])) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'ERROR_NO_EPIC_ACTIVE'));
        } elseif (Yii::$app->params['activeEpic']->epic_id <> $model->epic_id) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'ERROR_WRONG_EPIC'));
        }

        $model->recordSighting();

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new story
     * @return mixed
     */
    public function actionCreate()
    {
        if(!Story::canUserCreateThem()) {
            Story::throwExceptionAboutCreate();
        }

        $model = new Story();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->story_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing story
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if(!$model->canUserControlYou()) {
            Story::throwExceptionAboutControl();
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->story_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Moves story up in order; this means lower position on the list
     * @param int $id Story ID
     * @return \yii\web\Response
     */
    public function actionMoveUp($id)
    {
        $model = $this->findModel($id);
        if(!$model->canUserControlYou()) {
            Story::throwExceptionAboutControl();
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
     * Moves story down in order; this means higher position on the list
     * @param int $id Story ID
     * @return \yii\web\Response
     */
    public function actionMoveDown($id)
    {
        $model = $this->findModel($id);
        if(!$model->canUserControlYou()) {
            Story::throwExceptionAboutControl();
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
     * Finds the Story model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Story the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Story::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'PAGE_NOT_FOUND'));
        }
    }
}
