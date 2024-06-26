<?php

namespace backend\controllers;

use backend\controllers\tools\EpicAssistance;
use backend\controllers\tools\MarkChangeTrait;
use common\models\core\Visibility;
use common\models\Story;
use common\models\StoryQuery;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * StoryController implements the CRUD actions for Story model.
 */
final class StoryController extends Controller
{
    use EpicAssistance;
    use MarkChangeTrait;

    private const POSITIONS_PER_PAGE = 16;

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => [
                            'index',
                            'create',
                            'update',
                            'view',
                            'move-down',
                            'move-up',
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
        if (empty(Yii::$app->params['activeEpic'])) {
            return $this->render('../epic-selection');
        }

        if (!Story::canUserIndexThem()) {
            Story::throwExceptionAboutIndex();
        }

        $searchModel = new StoryQuery(self::POSITIONS_PER_PAGE);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single story
     * @param string $key
     * @return mixed
     */
    public function actionView($key)
    {
        $model = $this->findModel($key);

        if (empty(Yii::$app->params['activeEpic'])) {
            return $this->render('../epic-selection', ['objectEpic' => $model->epic]);
        }

        if (!$model->canUserViewYou()) {
            Story::throwExceptionAboutView();
        }

        if (empty(Yii::$app->params['activeEpic'])) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'ERROR_NO_EPIC_ACTIVE'));
        } elseif (Yii::$app->params['activeEpic']->epic_id <> $model->epic_id) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'ERROR_WRONG_EPIC'));
        }

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
        if (!Story::canUserCreateThem()) {
            Story::throwExceptionAboutCreate();
        }

        $model = new Story();

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
     * Updates an existing story
     * @param string $key
     * @return mixed
     */
    public function actionUpdate($key)
    {
        $model = $this->findModel($key);

        if (!$model->canUserControlYou()) {
            Story::throwExceptionAboutControl();
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'key' => $model->key]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Moves story up in order; this means lower position on the list
     * @param int $key Story ID
     * @return \yii\web\Response
     */
    public function actionMoveUp($key)
    {
        $model = $this->findModel($key);
        if (!$model->canUserControlYou()) {
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
     * @param int $key Story ID
     * @return \yii\web\Response
     */
    public function actionMoveDown($key)
    {
        $model = $this->findModel($key);
        if (!$model->canUserControlYou()) {
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
        $model = $this->findModel($key);
        $this->markChange($model);
        return $this->redirect(['view', 'key' => $model->key]);
    }

    /**
     * Finds the Story model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $key
     * @return Story the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel(string $key): Story
    {
        $model = Story::findOne(['key' => $key]);

        if ($model === null) {
            throw new NotFoundHttpException(Yii::t('app', 'STORY_NOT_AVAILABLE'));
        }

        if (!in_array($model->visibility, Visibility::determineVisibilityVector($model->epic))) {
            throw new NotFoundHttpException(Yii::t('app', 'STORY_NOT_AVAILABLE'));
        }

        $this->selectEpic($model->epic->key, $model->epic_id, $model->epic->name);

        return $model;
    }
}
