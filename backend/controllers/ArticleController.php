<?php

namespace backend\controllers;

use backend\controllers\tools\MarkChangeTrait;
use common\components\EpicAssistance;
use common\models\Article;
use common\models\ArticleQuery;
use common\models\Epic;
use Throwable;
use Yii;
use yii\base\InvalidRouteException;
use yii\db\Exception;
use yii\db\StaleObjectException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
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
     * Lists all Article models
     *
     * @throws HttpException
     */
    public function actionIndex(?string $epic = null): string
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

        if (!Article::canUserIndexThem()) {
            Article::throwExceptionAboutIndex();
        }

        $searchModel = new ArticleQuery();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'epic' => $epicObject ?? Yii::$app->params['activeEpic'],
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
     * @throws HttpException
     */
    public function actionView(string $key): string
    {
        $model = $this->findModelByKey($key);

        if (!$model->canUserViewYou()) {
            Article::throwExceptionAboutView();
        }

        return $this->render('view', ['model' => $model]);
    }

    /**
     * Creates a new Article model
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @throws Exception
     * @throws HttpException
     */
    public function actionCreate(?string $epic = null): Response|string
    {
        if (!Article::canUserCreateThem()) {
            Article::throwExceptionAboutCreate();
        }

        $model = new Article();

        $this->setEpicOnObject($epic, $model);
        if (!$model->isEpicSet()) {
            return $this->render('../epic-list');
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['article/view', 'key' => $model->key]);
        }

        return $this->render('create', ['model' => $model]);
    }

    /**
     * Updates an existing Article model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @throws NotFoundHttpException
     * @throws Exception
     * @throws HttpException
     */
    public function actionUpdate(string $key): Response|string
    {
        $model = $this->findModelByKey($key);

        if (!$model->canUserControlYou()) {
            Article::throwExceptionAboutControl();
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'key' => $model->key]);
        }

        return $this->render('update', ['model' => $model]);
    }

    /**
     * Deletes an existing Article model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param string $key
     *
     * @return Response
     *
     * @throws HttpException
     * @throws NotFoundHttpException
     * @throws StaleObjectException
     * @throws Throwable
     */
    public function actionDelete(string $key): Response
    {
        $model = $this->findModelByKey($key);

        if (!$model->canUserControlYou()) {
            Article::throwExceptionAboutControl();
        }

        $model->delete();

        return $this->redirect(['index', 'epic' => $model->epic->key]);
    }

    /**
     * Moves Article up in order; this means lower position on the list
     *
     * @throws NotFoundHttpException
     * @throws HttpException
     * @throws InvalidRouteException
     */
    public function actionMoveUp(string $key): Response
    {
        $model = $this->findModelByKey($key);
        if (!$model->canUserControlYou()) {
            Article::throwExceptionAboutControl();
        }
        $model->movePrev();

        $referrer = Yii::$app->getRequest()->getReferrer();
        if ($referrer) {
            return Yii::$app->getResponse()->redirect($referrer);
        }

        return $this->redirect(['index', 'epic' => $model->epic->key]);
    }

    /**
     * Moves Article down in order; this means higher position on the list
     *
     * @throws NotFoundHttpException
     * @throws HttpException
     * @throws InvalidRouteException
     */
    public function actionMoveDown(string $key): Response
    {
        $model = $this->findModelByKey($key);
        if (!$model->canUserControlYou()) {
            Article::throwExceptionAboutControl();
        }
        $model->moveNext();

        $referrer = Yii::$app->getRequest()->getReferrer();
        if ($referrer) {
            return Yii::$app->getResponse()->redirect($referrer);
        }

        return $this->redirect(['index', 'epic' => $model->epic->key]);
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
     * Finds the Article model based on its key value
     *
     * @throws NotFoundHttpException
     */
    protected function findModelByKey(string $key): Article
    {
        $model = Article::findOne(['key' => $key]);

        if ($model === null) {
            throw new NotFoundHttpException(Yii::t('app', 'ARTICLE_NOT_AVAILABLE'));
        }

        if ($model->isEpicSet()) {
            $this->selectEpic($model->epic->key, $model->epic_id, $model->epic->name);
        }

        return $model;
    }
}
