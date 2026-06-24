<?php

namespace backend\controllers;

use common\components\EpicAssistance;
use common\models\Epic;
use common\models\Recap;
use common\models\RecapQuery;
use Throwable;
use Yii;
use yii\base\InvalidRouteException;
use yii\db\Exception as DbException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

final class RecapController extends Controller
{
    use EpicAssistance;

    private const int POSITIONS_PER_PAGE = 16;

    public function behaviors(): array
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
     * @throws HttpException
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
     * @throws HttpException
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
     * @throws DbException
     * @throws HttpException
     */
    public function actionCreate(?string $epic = null): Response|string
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
     * @throws DbException
     * @throws HttpException
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
     * @throws HttpException
     */
    public function actionDelete(string $key): Response
    {
        $model = $this->findModel($key);

        if (!$model->canUserControlYou()) {
            Recap::throwExceptionAboutControl();
        }

        try {
            $success = $model->delete();
        } catch (Throwable) {
            $success = false;
        }

        Yii::$app->session->setFlash(
            $success ? 'success' : 'error',
            $success ? Yii::t('app', 'RECAP_DELETE_SUCCESS') : Yii::t('app', 'RECAP_DELETE_FAILED')
        );

        return $success
            ? $this->redirect(['recap/index', 'epic' => $model->epic->key])
            : $this->redirect(['recap/view', 'key' => $model->key]);
    }

    /**
     * Moves recap up in order; this means a lower position on the list
     *
     * @throws HttpException
     * @throws InvalidRouteException
     */
    public function actionMoveUp(string $key): Response
    {
        $model = $this->findModel($key);
        if (!$model->canUserControlYou()) {
            Recap::throwExceptionAboutControl();
        }

        $model->movePrev();

        $referrer = Yii::$app->getRequest()->getReferrer();
        if ($referrer) {
            return Yii::$app->getResponse()->redirect($referrer);
        }

        return $this->redirect(['index']);
    }

    /**
     * Moves recap down in order; this means a higher position on the list
     *
     * @throws HttpException
     * @throws InvalidRouteException
     */
    public function actionMoveDown(string $key): Response
    {
        $model = $this->findModel($key);
        if (!$model->canUserControlYou()) {
            Recap::throwExceptionAboutControl();
        }

        $model->moveNext();

        $referrer = Yii::$app->getRequest()->getReferrer();
        if ($referrer) {
            return Yii::$app->getResponse()->redirect($referrer);
        }

        return $this->redirect(['index']);
    }

    /**
     * @throws NotFoundHttpException
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
