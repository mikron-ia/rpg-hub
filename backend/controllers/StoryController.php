<?php

namespace backend\controllers;

use backend\controllers\tools\MarkChangeTrait;
use common\components\EpicAssistance;
use common\models\core\Visibility;
use common\models\Epic;
use common\models\Story;
use common\models\StoryQuery;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
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

        if (!Story::canUserIndexThem()) {
            Story::throwExceptionAboutIndex();
        }

        $searchModel = new StoryQuery(self::POSITIONS_PER_PAGE);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'epic' => $epicObject ?? Yii::$app->params['activeEpic'],
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single story
     */
    public function actionView(string $key): string
    {
        $model = $this->findModel($key);

        if (!$model->canUserViewYou()) {
            Story::throwExceptionAboutView();
        }

        return $this->render('view', ['model' => $model]);
    }

    /**
     * Creates a new story
     */
    public function actionCreate(string $epic = null): Response|string
    {
        if (!Story::canUserCreateThem()) {
            Story::throwExceptionAboutCreate();
        }

        $model = new Story();

        $this->setEpicOnObject($epic, $model);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['story/view', 'key' => $model->key]);
        }

        return $this->render('create', ['model' => $model]);
    }

    /**
     * Updates an existing story
     */
    public function actionUpdate(string $key): Response|string
    {
        $model = $this->findModel($key);

        if (!$model->canUserControlYou()) {
            Story::throwExceptionAboutControl();
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'key' => $model->key]);
        }

        return $this->render('update', ['model' => $model]);
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
