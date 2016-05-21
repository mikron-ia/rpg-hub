<?php

namespace backend\controllers;

use common\models\Epic;
use common\models\EpicQuery;
use Yii;
use common\models\StoryParameter;
use common\models\Story;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

/**
 * StoryController implements the CRUD actions for Story model.
 */
class StoryController extends Controller
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
                            'delete',
                            'update',
                            'view',
                            'parameter-create',
                            'parameter-update',
                            'parameter-delete'
                        ],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                    'parameter-delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Story models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Story::find()->orderBy('story_id DESC'),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Story model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Story model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Story();

        $epicListForSelector = EpicQuery::getListOfEpicsForSelector();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->story_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
                'epicListForSelector' => $epicListForSelector,
            ]);
        }
    }

    /**
     * Updates an existing Story model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $epicListForSelector = EpicQuery::getListOfEpicsForSelector();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->story_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
                'epicListForSelector' => $epicListForSelector,
            ]);
        }
    }

    /**
     * @param int $story_id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionParameterCreate($story_id)
    {
        $story = $this->findModel($story_id);
        $model = new StoryParameter();

        $model->story_id = $story->story_id;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->story_id]);
        } else {
            if (Yii::$app->request->isAjax) {
                return $this->renderAjax('story-parameter/create', ['model' => $model]);
            } else {
                return $this->render('story-parameter/create', ['model' => $model]);
            }
        }
    }

    /**
     * @param string $id
     * @return mixed
     * @throws HttpException
     */
    public function actionParameterUpdate($id)
    {
        $model = $this->findParameter($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->story_id]);
        } else {
            if (Yii::$app->request->isAjax) {
                return $this->renderAjax('story-parameter/update', ['model' => $model]);
            } else {
                return $this->render('story-parameter/update', ['model' => $model]);
            }
        }
    }

    /**
     * Deletes an existing StoryParameter model.
     * If deletion is successful, the browser will be redirected to the story page.
     * @param string $id
     * @return mixed
     */
    public function actionParameterDelete($id)
    {
        $model = $this->findParameter($id);

        $storyId = $model->story_id;
        $model->delete();

        return $this->redirect(['view', 'id' => $storyId]);
    }

    /**
     * Deletes an existing Story model.
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
            throw new NotFoundHttpException('The requested story does not exist.');
        }
    }

    /**
     * Finds the StoryParameter model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return StoryParameter the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findParameter($id)
    {
        if (($model = StoryParameter::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested story parameter does not exist.');
        }
    }
}
