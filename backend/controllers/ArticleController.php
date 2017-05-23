<?php

namespace backend\controllers;

use Yii;
use common\models\Article;
use common\models\ArticleQuery;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ArticleController implements the CRUD actions for Article model.
 */
class ArticleController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['create', 'index', 'update', 'view', 'delete', 'move-up', 'move-down'],
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

    /**
     * Lists all Article models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ArticleQuery();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Article model.
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
     * Creates a new Article model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Article();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->article_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Article model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->article_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Article model.
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
     * Moves game up in order; this means lower position on the list
     * @param int $id Story ID
     * @return \yii\web\Response
     */
    public function actionMoveUp($id)
    {
        $model = $this->findModel($id);
        if (!$model->canUserControlYou()) {
            Article::throwExceptionAboutControl();
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
            Article::throwExceptionAboutControl();
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
     * Finds the Article model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Article the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Article::findOne($id)) !== null) {
            if($model->epic_id) {
                if (empty(Yii::$app->params['activeEpic'])) {
                    $this->run('site/set-epic-in-silence', ['epicKey' => $model->epic->key]);
                    Yii::$app->session->setFlash('success', Yii::t('app', 'EPIC_SET_BASED_ON_OBJECT'));
                } elseif (Yii::$app->params['activeEpic']->epic_id <> $model->epic_id) {
                    $this->run('site/set-epic-in-silence', ['epicKey' => $model->epic->key]);
                    Yii::$app->session->setFlash('success', Yii::t('app', 'EPIC_CHANGED_BASED_ON_OBJECT'));
                }
            }
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'ARTICLE_NOT_AVAILABLE'));
        }
    }
}
