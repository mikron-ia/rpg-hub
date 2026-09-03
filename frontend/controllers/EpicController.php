<?php

namespace frontend\controllers;

use common\models\AnnouncementQuery;
use common\models\Epic;
use common\models\GameQuery;
use common\models\ProjectQuery;
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

final class EpicController extends Controller
{
    use EpicAssistance;

    private const int MAX_MOST_RECENT = 4;

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
     */
    public function actionView(string $key): string
    {
        $model = $this->findModelByKey($key);

        if (!$model->canUserViewYou()) {
            Epic::throwExceptionAboutView();
        }

        $this->selectEpic($model->key, $model->epic_id, $model->name);

        $model->recordSighting();

        /* Get Recap */
        $recap = new RecapQuery()->mostRecent();
        $recap?->recordSighting();

        /* Get Stories */
        $stories = new StoryQuery(self::MAX_MOST_RECENT)->search(Yii::$app->request->queryParams);
        $showCurrentStorySeparately =
            isset($model->current_story_id) &&
            !array_reduce(
                $stories->models,
                fn(bool $carry, Story $story) => $carry || $story->story_id === $model->current_story_id,
                false
            );

        /* Get Projects */
        $projects = new ProjectQuery(self::MAX_MOST_RECENT)->search(Yii::$app->request->queryParams);

        /* Get Sessions */
        $sessions = new GameQuery()->mostRecentDataProvider($model);

        try {
            $showScenarios = $model->canUserControlYou();
        } catch (HttpException) {
            $showScenarios = false;
        }

        /* Get News */
        $announcements = new AnnouncementQuery()->mostRecentDataProvider($model);

        return $this->render('view', [
            'epic' => $model,
            'sessions' => $sessions,
            'stories' => $stories,
            'projects' => $projects,
            'announcements' => $announcements,
            'recap' => $recap,
            'showScenarios' => $showScenarios,
            'showCurrentStorySeparately' => $showCurrentStorySeparately,
        ]);
    }

    /**
     * @throws NotFoundHttpException
     */
    protected function findModelByKey(string $key): Epic
    {
        return Epic::findOne(['key' => $key]) ?? throw new NotFoundHttpException(Yii::t('app', 'EPIC_NOT_AVAILABLE'));
    }
}
