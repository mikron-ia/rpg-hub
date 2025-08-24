<?php

namespace frontend\controllers;

use common\models\Announcement;
use common\models\AnnouncementQuery;
use common\models\Epic;
use common\components\EpicAssistance;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

/**
 * AnnouncementController implements the CRUD actions for Announcement model.
 */
class AnnouncementController extends Controller
{
    use EpicAssistance;

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => ['@'],
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
     * Lists all Announcement models
     *
     * @param null|string $key
     *
     * @return string
     *
     * @throws HttpException
     * @throws NotFoundHttpException
     */
    public function actionIndex(?string $key = null): string
    {
        if ($key) {
            $epic = $this->findEpicByKey($key);

            if (!$epic->canUserViewYou()) {
                Epic::throwExceptionAboutView();
            }

            $this->selectEpic($epic->key, $epic->epic_id, $epic->name);
        }

        if (empty(Yii::$app->params['activeEpic'])) {
            return $this->render('../epic-selection');
        }

//        if (!Announcement::canUserIndexThem()) {
//            Announcement::throwExceptionAboutIndex();
//        }

        $searchModel = new AnnouncementQuery();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModelByKey(string $key): Announcement
    {
        $model = Announcement::findOne(['key' => $key]);

        if ($model === null) {
            throw new NotFoundHttpException(Yii::t('app', 'ANNOUNCEMENT_NOT_AVAILABLE'));
        }

        return $model;
    }

    /**
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModelById(string $id): Announcement
    {
        $model = Announcement::findOne(['announcement_id' => $id]);

        if ($model === null) {
            throw new NotFoundHttpException(Yii::t('app', 'ANNOUNCEMENT_NOT_AVAILABLE'));
        }

        return $model;
    }
}
