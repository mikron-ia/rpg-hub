<?php

namespace frontend\controllers;

use common\models\AnnouncementQuery;
use common\models\Epic;
use common\models\GameQuery;
use common\models\RecapQuery;
use common\models\Story;
use common\models\StoryQuery;
use common\components\EpicAssistance;
use Override;
use Yii;
use yii\db\Exception;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

/**
 * Site controller
 */
final class EpicController extends Controller
{
    use EpicAssistance;

    #[Override]
    public function behaviors(): array
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

    /**
     * @return array<string, array<string,string|null>>
     */
    #[Override]
    public function actions(): array
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * @throws Exception
     * @throws HttpException
     * @throws NotFoundHttpException
     */
    public function actionView(string $key): string
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
        $recap?->recordSighting();

        /* Get Stories */
        $searchModel = new StoryQuery(4);
        $stories = $searchModel->search(Yii::$app->request->queryParams);
        $showCurrentStorySeparately =
            isset($model->current_story_id) &&
            !array_reduce(
                $stories->models,
                fn(bool $carry, Story $story) => $carry || $story->story_id === $model->current_story_id,
                false
            );

        /* Get Sessions */
        $sessionQuery = new GameQuery();
        $sessions = $sessionQuery->mostRecentDataProvider($model, true);
        $recap?->recordSighting();

        try {
            $showScenarios = $model->canUserControlYou();
        } catch (HttpException) {
            $showScenarios = false;
        }

        /* Get News */
        $announcementsQuery = new AnnouncementQuery();
        $announcements = $announcementsQuery->mostRecentDataProvider($model);

        return $this->render('view', [
            'epic' => $model,
            'sessions' => $sessions,
            'stories' => $stories,
            'announcements' => $announcements,
            'recap' => $recap,
            'showScenarios' => $showScenarios,
            'showCurrentStorySeparately' => $showCurrentStorySeparately,
        ]);
    }

    /**
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModelByKey(string $key): Epic
    {
        $model = Epic::findOne(['key' => $key]);

        if ($model === null) {
            throw new NotFoundHttpException(Yii::t('app', 'EPIC_NOT_AVAILABLE'));
        }

        return $model;
    }
}
