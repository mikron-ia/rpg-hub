<?php

namespace backend\controllers;

use common\models\Announcement;
use common\models\AnnouncementQuery;
use Yii;
use yii\db\Exception;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * AnnouncementController implements the CRUD actions for Announcement model
 */
class AnnouncementController extends Controller
{
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
        $searchModel = new AnnouncementQuery();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionView(string $key): string
    {
        return $this->render('view', [
            'model' => $this->findModelByKey($key),
        ]);
    }

    /**
     * @throws Exception
     */
    public function actionCreate(): Response|string
    {
        $model = new Announcement();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['announcement/view', 'key' => $model->key]);
        }

        return $this->render('create', [
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
