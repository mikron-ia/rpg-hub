<?php

namespace backend\controllers;

use common\components\EpicAssistance;
use common\models\Epic;
use common\models\Game;
use common\models\GameQuery;
use Override;
use Throwable;
use Yii;
use yii\base\InvalidRouteException;
use yii\db\Exception;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class GameController extends Controller
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
                    'delete' => ['POST'],
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
    public function actionView(int $id): string
    {
        $model = $this->findModel($id);

        if (!$model->canUserViewYou()) {
            Game::throwExceptionAboutView();
        }

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new Game model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
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
            return $this->redirect(['game/view', 'id' => $model->game_id]);
        }

        return $this->render('create', ['model' => $model]);
    }

    /**
     * @throws Exception
     * @throws HttpException
     * @throws NotFoundHttpException
     */
    public function actionUpdate(int $id): Response|string
    {
        $model = $this->findModel($id);

        if (!$model->canUserControlYou()) {
            Game::throwExceptionAboutControl();
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->game_id]);
        }

        return $this->render('update', ['model' => $model]);
    }

    /**
     * @throws Exception
     * @throws HttpException
     * @throws NotFoundHttpException
     * @throws Throwable
     */
    public function actionDelete(int $id): Response
    {
        $model = $this->findModel($id);

        if (!$model->canUserControlYou()) {
            Game::throwExceptionAboutControl();
        }

        $model->delete();

        return $this->redirect(['game/index', 'epic' => $model->epic->key]);
    }

    /**
     * Moves game up in order; this means a lower position on the list
     *
     * @throws HttpException
     * @throws InvalidRouteException
     * @throws NotFoundHttpException
     */
    public function actionMoveUp(int $id): Response
    {
        $model = $this->findModel($id);
        if (!$model->canUserControlYou()) {
            Game::throwExceptionAboutControl();
        }
        $model->movePrev();

        $referrer = Yii::$app->getRequest()->getReferrer();
        if ($referrer) {
            return Yii::$app->getResponse()->redirect($referrer);
        }

        return $this->redirect(['index']);
    }

    /**
     * Moves game down in order; this means higher position on the list
     *
     * @throws HttpException
     * @throws InvalidRouteException
     * @throws NotFoundHttpException
     */
    public function actionMoveDown(int $id): Response
    {
        $model = $this->findModel($id);
        if (!$model->canUserControlYou()) {
            Game::throwExceptionAboutControl();
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
    protected function findModel(int $id): Game
    {
        $model = Game::findOne($id);

        if ($model === null) {
            throw new NotFoundHttpException(Yii::t('app', 'GAME_SESSION_NOT_AVAILABLE'));
        }

        $this->selectEpic($model->epic->key, $model->epic_id, $model->epic->name);

        return $model;
    }
}
