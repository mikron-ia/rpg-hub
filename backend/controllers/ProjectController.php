<?php

namespace backend\controllers;

use backend\controllers\tools\MarkChangeTrait;
use common\components\EpicAssistance;
use common\models\core\Visibility;
use common\models\Epic;
use common\models\Parameter;
use common\models\Project;
use common\models\ProjectQuery;
use Override;
use Yii;
use yii\base\InvalidRouteException;
use yii\db\Exception;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

final class ProjectController extends CmsController
{
    use EpicAssistance;
    use MarkChangeTrait;

    private const int POSITIONS_PER_PAGE = 16;

    #[Override]
    public function behaviors(): array
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
                            'mark-changed',
                            'create-parameter',
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

        if (!Project::canUserIndexThem()) {
            Project::throwExceptionAboutIndex();
        }

        $searchModel = new ProjectQuery(self::POSITIONS_PER_PAGE);
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
            Project::throwExceptionAboutView();
        }

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * @throws Exception
     * @throws HttpException
     */
    public function actionCreate(?string $epic = null): Response|string
    {
        if (!Project::canUserCreateThem()) {
            Project::throwExceptionAboutCreate();
        }

        $model = new Project();

        $this->setEpicOnObject($epic, $model);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['project/view', 'key' => $model->key]);
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
            Project::throwExceptionAboutControl();
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'key' => $model->key]);
        }

        return $this->render('update', ['model' => $model]);
    }

    /**
     * Moves a project up in order; this means a lower position on the list
     *
     * @throws HttpException
     * @throws InvalidRouteException
     * @throws NotFoundHttpException
     */
    public function actionMoveUp(string $key): Response
    {
        $model = $this->findModel($key);

        if (!$model->canUserControlYou()) {
            Project::throwExceptionAboutControl();
        }

        $model->movePrev();

        return $this->returnToReferrer(['index']);
    }

    /**
     * Moves a project down in order; this means higher position on the list
     *
     * @throws HttpException
     * @throws InvalidRouteException
     */
    public function actionMoveDown($key): Response
    {
        $model = $this->findModel($key);

        if (!$model->canUserControlYou()) {
            Project::throwExceptionAboutControl();
        }

        $model->moveNext();

        return $this->returnToReferrer(['index']);
    }

    /**
     * @throws HttpException
     * @throws NotFoundHttpException
     */
    public function actionMarkChanged(string $key): Response
    {
        $model = $this->findModel($key);

        if (!$model->canUserControlYou()) {
            Project::throwExceptionAboutControl();
        }

        $this->markChange($model);

        return $this->redirect(['view', 'key' => $model->key]);
    }

    /**
     * @throws Exception
     * @throws HttpException
     */
    public function actionCreateParameter(string $key): Response|string
    {
        $model = $this->findModel($key);
        if (!$model->canUserControlYou()) {
            Project::throwExceptionAboutControl();
        }

        $parameter = new Parameter();
        $loadSuccess = $parameter->load(Yii::$app->request->post());
        $parameter->parameter_pack_id = $model->parameterPack->parameter_pack_id;

        if ($loadSuccess && $parameter->save()) {
            return $this->returnToReferrer(['site/index']);
        }

        $dataForCreate = [
            'model' => $parameter,
            'creatorController' => 'project',
            'creatorKey' => $model->key,
        ];

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('../parameter/create', $dataForCreate);
        }

        return $this->render('../parameter/create', $dataForCreate);
    }

    /**
     * @throws NotFoundHttpException
     */
    protected function findModel(string $key): Project
    {
        $model = Project::findOne(['key' => $key]);

        if ($model === null) {
            throw new NotFoundHttpException(Yii::t('app', 'PROJECT_NOT_AVAILABLE'));
        }

        if (!in_array($model->getVisibility(), Visibility::determineVisibilityVectorWithObjects($model->epic))) {
            throw new NotFoundHttpException(Yii::t('app', 'PROJECT_NOT_AVAILABLE'));
        }

        $this->selectEpic($model->epic->key, $model->epic_id, $model->epic->name);

        return $model;
    }
}
