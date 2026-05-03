<?php

namespace backend\controllers;

use backend\controllers\tools\MarkChangeTrait;
use common\components\EpicAssistance;
use common\components\service\DescriptionService;
use common\models\core\Visibility;
use common\models\Description;
use common\models\Epic;
use common\models\Location;
use common\models\LocationQuery;
use Override;
use Yii;
use yii\db\Exception;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\ForbiddenHttpException;
use yii\web\HttpException;
use yii\web\MethodNotAllowedHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

final class LocationController extends CmsController
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
                            'create',
                            'create-description',
                            'display-descriptions',
                            'index',
                            'index-importance',
                            'update',
                            'view',
                            'mark-changed',
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
     * Lists all Location models.
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

        if (!Location::canUserIndexThem()) {
            Location::throwExceptionAboutIndex();
        }

        $searchModel = new LocationQuery(self::POSITIONS_PER_PAGE);
        $dataProvider = $searchModel->searchForOperator(Yii::$app->request->queryParams);

        return $this->render('index', [
            'epic' => $epicObject ?? Yii::$app->params['activeEpic'],
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all locations with their importances
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

        if (!Location::canUserIndexThem()) {
            Location::throwExceptionAboutIndex();
        }

        $searchModel = new LocationQuery(self::POSITIONS_PER_PAGE);
        $dataProvider = $searchModel->listForOperatorWithImportances(Yii::$app->request->queryParams);

        return $this->render('index_importances', [
            'epic' => $epicObject,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Location model
     *
     * @throws HttpException
     * @throws NotFoundHttpException
     */
    public function actionView(string $key): string
    {
        $model = $this->findModelByKey($key);

        if (!$model->canUserViewYou()) {
            Location::throwExceptionAboutView();
        }

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new Location model
     *
     * @throws Exception
     * @throws HttpException
     */
    public function actionCreate(string $epic = null): Response|string
    {
        if (!Location::canUserCreateThem()) {
            Location::throwExceptionAboutCreate();
        }

        $model = new Location();

        $this->setEpicOnObject($epic, $model);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'key' => $model->key]);
        }

        return $this->render('create', ['model' => $model]);
    }

    /**
     * Updates an existing Location model
     *
     * @throws Exception
     * @throws HttpException
     */
    public function actionUpdate(string $key): Response|string
    {
        $model = $this->findModelByKey($key);

        if (!$model->canUserControlYou()) {
            Location::throwExceptionAboutControl();
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
     * Finds the Location model based on its primary key value
     *
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModelByKey(string $key): Location
    {
        $model = Location::findOne(['key' => $key]);

        if ($model === null) {
            throw new NotFoundHttpException(Yii::t('app', 'LOCATION_NOT_AVAILABLE'));
        }

        $this->selectEpic($model->epic->key, $model->epic_id, $model->epic->name);

        if (!in_array($model->visibility, Visibility::determineVisibilityVector($model->epic))) {
            throw new NotFoundHttpException(Yii::t('app', 'LOCATION_NOT_AVAILABLE'));
        }

        $this->selectEpic($model->epic->key, $model->epic_id, $model->epic->name);

        return $model;
    }
}
