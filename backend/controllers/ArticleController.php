<?php

namespace backend\controllers;

use backend\controllers\tools\EpicAssistance;
use backend\controllers\tools\MarkChangeTrait;
use Yii;
use common\models\Article;
use common\models\ArticleQuery;
use yii\base\InvalidRouteException;
use yii\db\Exception;
use yii\db\StaleObjectException;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * ArticleController implements the CRUD actions for Article model.
 */
class ArticleController extends Controller
{
    use EpicAssistance;
    use MarkChangeTrait;

    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => [
                            'create',
                            'index',
                            'update',
                            'view',
                            'delete',
                            'move-up',
                            'move-down',
                            'mark-changed'
                        ],
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
     * Lists all Article models.
     *
     * @return string
     */
    public function actionIndex(): string
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
     *
     * @param string $key
     *
     * @return string
     *
     * @throws NotFoundHttpException
     */
    public function actionView(string $key): string
    {
        return $this->render('view', [
            'model' => $this->findModelByKey($key),
        ]);
    }

    /**
     * Creates a new Article model
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return Response|string
     *
     * @throws Exception
     */
    public function actionCreate(): Response|string
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
     *
     * @param string $key
     *
     * @return Response|string
     *
     * @throws NotFoundHttpException
     * @throws Exception
     */
    public function actionUpdate(string $key): Response|string
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
     *
     * @param string $key
     *
     * @return Response
     *
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function actionDelete(string $key): Response
    {
        $this->findModelByKey($key)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Moves game up in order; this means lower position on the list
     *
     * @param int $key Story ID
     *
     * @throws NotFoundHttpException
     * @throws HttpException
     * @throws InvalidRouteException
     */
    public function actionMoveUp(int $key): Response|\yii\console\Response
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
     *
     * @param int $key Story ID
     *
     * @return Response
     *
     * @throws NotFoundHttpException
     * @throws HttpException
     * @throws InvalidRouteException
     */
    public function actionMoveDown(int $key): Response
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
     * Saves the model to mark it as changed
     *
     * @param string $key
     *
     * @return Response
     *
     * @throws HttpException
     * @throws NotFoundHttpException
     */
    public function actionMarkChanged(string $key): Response
    {
        $model = $this->findModelByKey($key);
        $this->markChange($model);
        return $this->redirect(['view', 'key' => $model->key]);
    }

    /**
     * Finds the Article model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param string $key
     *
     * @return Article the loaded model
     *
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModelByKey($key): Article
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
