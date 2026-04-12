<?php

namespace backend\controllers;

use backend\controllers\tools\MarkChangeTrait;
use common\components\EpicAssistance;
use common\models\core\Visibility;
use common\models\Epic;
use common\models\Group;
use common\models\GroupQuery;
use common\models\StoryGroupAssignmentQuery;
use Yii;
use yii\db\Exception;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\HttpException;
use yii\web\MethodNotAllowedHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * GroupController implements the CRUD actions for the Group model.
 */
final class GroupController extends Controller
{
    use EpicAssistance;
    use MarkChangeTrait;

    private const int POSITIONS_PER_PAGE = 16;

    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['create', 'display-descriptions', 'index', 'index-importance', 'update', 'view', 'mark-changed'],
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
     * Lists all Group models.
     *
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

        if (!Group::canUserIndexThem()) {
            Group::throwExceptionAboutIndex();
        }

        $searchModel = new GroupQuery(self::POSITIONS_PER_PAGE);
        $dataProvider = $searchModel->searchForOperator(Yii::$app->request->queryParams);

        return $this->render('index', [
            'epic' => $epicObject ?? Yii::$app->params['activeEpic'],
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all groups with their importances
     *
     * @throws HttpException
     */
    public function actionIndexImportance(string $epic): string
    {
        $epicObject = $this->findEpicByKey($epic);

        if (!$epicObject->canUserViewYou()) {
            Epic::throwExceptionAboutView();
        }

        $this->selectEpic($epicObject->key, $epicObject->epic_id, $epicObject->name);

        if (empty(Yii::$app->params['activeEpic'])) {
            return $this->render('../epic-list');
        }

        if (!Group::canUserIndexThem()) {
            Group::throwExceptionAboutIndex();
        }

        $searchModel = new GroupQuery(self::POSITIONS_PER_PAGE);
        $dataProvider = $searchModel->listForOperatorWithImportances(Yii::$app->request->queryParams);

        return $this->render('index_importances', [
            'epic' => $epicObject,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Group model
     *
     * @throws HttpException
     * @throws NotFoundHttpException
     */
    public function actionView(string $key): string
    {
        $model = $this->findModelByKey($key);

        if (!$model->canUserViewYou()) {
            Group::throwExceptionAboutView();
        }

        return $this->render('view', [
            'model' => $model,
            'storyGroupPublic' => StoryGroupAssignmentQuery::getStoryAssignmentPublicLinksForOperator($model->group_id),
            'storyGroupPrivate' => StoryGroupAssignmentQuery::getStoryAssignmentPrivateLinksForOperator($model->group_id),
        ]);
    }

    /**
     * Creates a new Group model
     *
     * @throws Exception
     * @throws HttpException
     */
    public function actionCreate(string $epic = null): Response|string
    {
        if (!Group::canUserCreateThem()) {
            Group::throwExceptionAboutCreate();
        }

        $model = new Group();

        $this->setEpicOnObject($epic, $model);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'key' => $model->key]);
        }

        return $this->render('create', ['model' => $model]);
    }

    /**
     * Updates an existing Group model
     *
     * @throws Exception
     * @throws HttpException
     */
    public function actionUpdate(string $key): Response|string
    {
        $model = $this->findModelByKey($key);

        if (!$model->canUserControlYou()) {
            Group::throwExceptionAboutControl();
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'key' => $model->key]);
        }

        return $this->render('update', ['model' => $model]);
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
     * @throws HttpException
     */
    public function actionDisplayDescriptions(string $key): string
    {
        if (!Yii::$app->request->isAjax) {
            throw new MethodNotAllowedHttpException(Yii::t('app', 'ERROR_AJAX_REQUESTS_ONLY'));
        }

        $model = $this->findModelByKey($key);

        if (!$model->canUserControlYou()) {
            throw new ForbiddenHttpException(Yii::t('app', 'DESCRIPTION_PACK_NOT_ACCESSIBLE'));
        }

        return $this->renderAjax('../description/_view_descriptions', ['model' => $model->descriptionPack]);
    }

    /**
     * Finds the Group model based on its primary key value
     *
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModelByKey(string $key): Group
    {
        $model = Group::findOne(['key' => $key]);

        if ($model === null) {
            throw new NotFoundHttpException(Yii::t('app', 'GROUP_NOT_AVAILABLE'));
        }

        $this->selectEpic($model->epic->key, $model->epic_id, $model->epic->name);

        if (!in_array($model->visibility, Visibility::determineVisibilityVector($model->epic))) {
            throw new NotFoundHttpException(Yii::t('app', 'GROUP_NOT_AVAILABLE'));
        }

        $this->selectEpic($model->epic->key, $model->epic_id, $model->epic->name);

        return $model;
    }
}
