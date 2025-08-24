<?php

namespace backend\controllers;

use common\components\EpicAssistance;
use common\models\Announcement;
use common\models\AnnouncementQuery;
use common\models\Epic;
use Yii;
use yii\db\Exception;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * AnnouncementController implements the CRUD actions for Announcement model
 */
class AnnouncementController extends Controller
{
    use EpicAssistance;

    public function behaviors(): array
    {
        return array_merge(
            parent::behaviors(),
            [
                'access' => [
                    'class' => AccessControl::class,
                    'rules' => [
                        [
                            'actions' => [
                                'create',
                                'index',
                                'update',
                                'view',
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
            ]
        );
    }

    public function actionIndex(): string
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

        if (!Announcement::canUserIndexThem()) {
            Announcement::throwExceptionAboutIndex();
        }

        $searchModel = new AnnouncementQuery();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'epic' => $epicObject ?? Yii::$app->params['activeEpic'],
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @throws NotFoundHttpException
     * @throws HttpException
     */
    public function actionView(string $key): string
    {
        if (!empty($epic)) {
            $epicObject = $this->findEpicByKey($epic);

            if (!$epicObject->canUserViewYou()) {
                Epic::throwExceptionAboutView();
            }

            $this->selectEpic($epicObject->key, $epicObject->epic_id, $epicObject->name);
        }

        $model = $this->findModelByKey($key);

        return $this->render('view', [
            'model' => $model,
            'epic' => $epicObject ?? Yii::$app->params['activeEpic'],
        ]);
    }

    /**
     * @throws Exception
     * @throws HttpException
     * @throws NotFoundHttpException
     */
    public function actionCreate(string $epic = null): Response|string
    {
        if (!Announcement::canUserCreateThem()) {
            Announcement::throwExceptionAboutCreate();
        }

        if (!empty($epic)) {
            $epicObject = $this->findEpicByKey($epic);

            if (!$epicObject->canUserViewYou()) {
                Epic::throwExceptionAboutView();
            }

            $this->selectEpic($epicObject->key, $epicObject->epic_id, $epicObject->name);
        }

        $model = new Announcement();

        $this->setEpicOnObject($epic, $model);
        if (!$model->isEpicSet()) {
            return $this->render('../epic-list');
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['announcement/view', 'key' => $model->key]);
        }

        return $this->render('create', [
            'epic' => $epicObject ?? Yii::$app->params['activeEpic'],
            'model' => $model,
        ]);
    }

    /**
     * @throws NotFoundHttpException
     * @throws Exception
     */
    public function actionUpdate(string $key): Response|string
    {
        $model = $this->findModelByKey($key);

        if (!$model->canUserControlYou()) {
            Announcement::throwExceptionAboutControl();
        }

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'key' => $model->key]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @throws NotFoundHttpException
     */
    protected function findModel(int $announcement_id): Announcement
    {
        if (($model = Announcement::findOne(['announcement_id' => $announcement_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'ANNOUNCEMENT_NOT_AVAILABLE'));
    }

    /**
     * @throws NotFoundHttpException
     */
    protected function findModelByKey(string $key): Announcement
    {
        if (($model = Announcement::findOne(['key' => $key])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'ANNOUNCEMENT_NOT_AVAILABLE'));
    }
}
