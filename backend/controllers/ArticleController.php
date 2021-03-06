<?php

namespace backend\controllers;

use backend\controllers\tools\EpicAssistance;
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
    use EpicAssistance;

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
     * @param string $key
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionView($key)
    {
        return $this->render('view', [
            'model' => $this->findModelByKey($key),
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
     * Updates an existing Article model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $key
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionUpdate($key)
    {
        $model = $this->findModelByKey($key);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'key' => $model->key]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Article model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $key
     * @return mixed
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($key)
    {
        $this->findModelByKey($key)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Moves game up in order; this means lower position on the list
     * @param int $key Story ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     * @throws \yii\web\HttpException
     */
    public function actionMoveUp($key)
    {
        $model = $this->findModelByKey($key);
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
     * @param int $key Story ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     * @throws \yii\web\HttpException
     */
    public function actionMoveDown($key)
    {
        $model = $this->findModelByKey($key);
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
     * @param string $key
     * @return Article the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModelByKey($key)
    {
        if (($model = Article::findOne(['key' => $key])) !== null) {
            if($model->epic_id) {
                $this->selectEpic($model->epic->key, $model->epic_id, $model->epic->name);
            }
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'ARTICLE_NOT_AVAILABLE'));
        }
    }
}
