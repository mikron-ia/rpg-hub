<?php

namespace backend\controllers;

use common\components\EpicAssistance;
use common\models\Epic;
use common\models\Game;
use common\models\GameQuery;
use Override;
use Throwable;
use Yii;
use yii\db\Exception;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class GameController extends CmsController
{
    use EpicAssistance;

    #[Override]
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['create', 'index', 'update', 'view', 'delete', 'move-up', 'move-down'],
                        'allow' => true,
                        'roles' => ['operator'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['DELETE'],
                ],
            ],
        ];
    }

    /**
     * @throws HttpException
     * @throws NotFoundHttpException
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

        if (!Game::canUserIndexThem()) {
            Game::throwExceptionAboutIndex();
        }

        $searchModel = new GameQuery();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'epic' => $epicObject ?? Yii::$app->params['activeEpic'],
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @throws HttpException
     * @throws NotFoundHttpException
     */
    public function actionView(string $key): string
    {
        $model = $this->findModel($key);

        if (!$model->canUserViewYou()) {
            Game::throwExceptionAboutView();
        }

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * @throws Exception
     * @throws HttpException
     */
    public function actionCreate(string $epic = null): Response|string
    {
        if (!Game::canUserCreateThem()) {
            Game::throwExceptionAboutCreate();
        }

        $model = new Game();

        $this->setEpicOnObject($epic, $model);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['game/view', 'key' => $model->key]);
        }

        return $this->render('create', ['model' => $model]);
    }

    /**
     * @throws Exception
     * @throws HttpException
     * @throws NotFoundHttpException
     */
    public function actionUpdate(string $key): Response|string
    {
        $model = $this->findModel($key);

        if (!$model->canUserControlYou()) {
            Game::throwExceptionAboutControl();
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'key' => $model->key]);
        }

        return $this->render('update', ['model' => $model]);
    }

    /**
     * @throws HttpException
     * @throws NotFoundHttpException
     */
    public function actionDelete(string $key): Response
    {
        $model = $this->findModel($key);

        if (!$model->canUserControlYou()) {
            Game::throwExceptionAboutControl();
        }

        try {
            $success = $model->delete();
        } catch (Throwable) {
            $success = false;
        }

        Yii::$app->session->setFlash(
            $success ? 'success' : 'error',
            $success ? Yii::t('app', 'GAME_SESSION_DELETE_SUCCESS') : Yii::t('app', 'GAME_SESSION_DELETE_FAILED')
        );

        return $success
            ? $this->redirect(['game/index', 'epic' => $model->epic->key])
            : $this->redirect(['game/view', 'key' => $model->key]);
    }

    /**
     * Moves game up in order; this means a lower position on the list
     *
     * @throws HttpException
     * @throws NotFoundHttpException
     */
    public function actionMoveUp(string $key): Response
    {
        $model = $this->findModel($key);
        if (!$model->canUserControlYou()) {
            Game::throwExceptionAboutControl();
        }
        $model->movePrev();

        return $this->returnToReferrer(['index']);
    }

    /**
     * Moves game down in order; this means higher position on the list
     *
     * @throws HttpException
     * @throws NotFoundHttpException
     */
    public function actionMoveDown(string $key): Response
    {
        $model = $this->findModel($key);
        if (!$model->canUserControlYou()) {
            Game::throwExceptionAboutControl();
        }
        $model->moveNext();

        return $this->returnToReferrer(['index']);
    }

    /**
     * @throws NotFoundHttpException
     */
    protected function findModel(string $key): Game
    {
        $model = Game::findOne(['key' => $key]);

        if ($model === null) {
            throw new NotFoundHttpException(Yii::t('app', 'GAME_SESSION_NOT_AVAILABLE'));
        }

        $this->selectEpic($model->epic->key, $model->epic_id, $model->epic->name);

        return $model;
    }
}
