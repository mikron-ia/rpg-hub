<?php

namespace frontend\controllers;

use common\models\Epic;
use common\models\GameQuery;
use common\models\RecapQuery;
use common\models\StoryQuery;
use frontend\controllers\tools\EpicAssistance;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * Site controller
 */
final class EpicController extends Controller
{
    use EpicAssistance;

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['captcha', 'error', 'login'],
                        'allow' => true,
                    ],
                    [
                        'actions' => [
                            'view',
                        ],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Displays Epic
     * @param $key
     * @return string
     * @throws NotFoundHttpException
     * @throws \yii\web\HttpException
     */
    public function actionView($key)
    {
        /* Get Epic */
        $model = $this->findModelByKey($key);

        if (!$model->canUserViewYou()) {
            Epic::throwExceptionAboutView();
        }

        $this->selectEpic($model->key, $model->epic_id, $model->name);

        $model->recordSighting();

        /* Get Recap */
        $recapQuery = new RecapQuery();
        $recap = $recapQuery->mostRecent();

        if ($recap) {
            $recap->recordSighting();
        }

        /* Get Stories */
        $searchModel = new StoryQuery(4);
        $stories = $searchModel->search(Yii::$app->request->queryParams);

        /* Get Sessions */
        $sessionQuery = new GameQuery();
        $sessions = $sessionQuery->mostRecentDataProvider($model);

        if ($recap) {
            $recap->recordSighting();
        }

        /* Get News */
        $news = [];

        return $this->render('view', [
            'epic' => $model,
            'sessions' => $sessions,
            'stories' => $stories,
            'news' => $news,
            'recap' => $recap,
        ]);
    }

    /**
     * Finds the Story model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $key
     * @return Epic the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModelByKey($key)
    {
        $model = Epic::findOne(['key' => $key]);

        if ($model === null) {
            throw new NotFoundHttpException(Yii::t('app', 'EPIC_NOT_AVAILABLE'));
        }

        return $model;
    }
}
