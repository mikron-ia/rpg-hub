<?php

namespace frontend\controllers;

use common\models\Epic;
use common\models\GameQuery;
use common\models\RecapQuery;
use common\models\StoryQuery;
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
                            'index',
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

    public function actionIndex()
    {
        throw new \HTTP_Request2_NotImplementedException();
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

        if (empty(Yii::$app->params['activeEpic'])) {
            $this->run('site/set-epic-in-silence', ['epicKey' => $model->key]);
            Yii::$app->session->setFlash('success', Yii::t('app', 'EPIC_SET_BASED_ON_OBJECT'));
        } elseif (Yii::$app->params['activeEpic']->epic_id <> $model->epic_id) {
            $this->run('site/set-epic-in-silence', ['epicKey' => $model->key]);
            Yii::$app->session->setFlash('success', Yii::t('app', 'EPIC_CHANGED_BASED_ON_OBJECT'));
        }

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
