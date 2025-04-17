<?php

namespace backend\controllers;

use backend\controllers\tools\EpicAssistance;
use backend\controllers\tools\MarkChangeTrait;
use common\models\core\Visibility;
use common\models\Epic;
use common\models\Group;
use common\models\GroupQuery;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * GroupController implements the CRUD actions for Group model.
 */
final class GroupController extends Controller
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
                        'actions' => ['create', 'index', 'update', 'view', 'mark-changed'],
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
     * Displays a single Group model
     */
    public function actionView(string $key): string
    {
        $model = $this->findModelByKey($key);

        if (!$model->canUserViewYou()) {
            Group::throwExceptionAboutView();
        }

        return $this->render('view', ['model' => $model]);
    }

    /**
     * Creates a new Group model.
     * If creation is successful, the browser will be redirected to the 'view' page.
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
     * Updates an existing Group model.
     * If update is successful, the browser will be redirected to the 'view' page.
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
     * Finds the Group model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param string $key
     *
     * @return Group the loaded model
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
