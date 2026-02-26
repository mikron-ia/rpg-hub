<?php

namespace frontend\controllers;

use common\models\core\Visibility;
use common\models\Epic;
use common\models\Story;
use common\models\StoryCharacterAssignmentQuery;
use common\models\StoryGroupAssignmentQuery;
use common\models\StoryQuery;
use common\components\EpicAssistance;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

/**
 * StoryController implements the CRUD actions for the Story model.
 */
final class StoryController extends Controller
{
    use EpicAssistance;

    private const POSITIONS_PER_PAGE = 4;

    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['index', 'view'],
                        'allow' => true,
                        'roles' => ['user'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all Story models
     *
     * @throws NotFoundHttpException
     * @throws HttpException
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

        if (!Story::canUserIndexThem()) {
            Story::throwExceptionAboutIndex();
        }

        $searchModel = new StoryQuery(self::POSITIONS_PER_PAGE);
        $dataProvider = $searchModel->search([]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Story model
     *
     * @throws HttpException
     */
    public function actionView(string $key): string
    {
        $model = $this->findModelByKey($key);

        if (!$model->canUserViewYou()) {
            Story::throwExceptionAboutView();
        }

        $this->selectEpic($model->epic->key, $model->epic_id, $model->epic->name);

        $model->recordSighting();

        if ($model->canUserControlYou()) { // not the best, but only real possible way of verifying user access level
            $storyCharacterPublic = StoryCharacterAssignmentQuery::getCharacterAssignmentPublicLinksForOperator($model->story_id);
            $storyCharacterPrivate = StoryCharacterAssignmentQuery::getCharacterAssignmentPrivateLinksForOperator($model->story_id);
            $storyGroupPublic = StoryGroupAssignmentQuery::getGroupAssignmentPublicLinksForOperator($model->story_id);
            $storyGroupPrivate = StoryGroupAssignmentQuery::getGroupAssignmentPrivateLinksForOperator($model->story_id);
        } else {
            $storyCharacterPublic = StoryCharacterAssignmentQuery::getCharacterAssignmentLinksForUser($model->story_id);
            $storyCharacterPrivate = [];
            $storyGroupPublic = StoryGroupAssignmentQuery::getGroupAssignmentLinksForUser($model->story_id);
            $storyGroupPrivate = [];
        }

        return $this->render('view', [
            'model' => $model,
            'storyCharacterPublic' => $storyCharacterPublic,
            'storyCharacterPrivate' => $storyCharacterPrivate,
            'storyGroupPublic' => $storyGroupPublic,
            'storyGroupPrivate' => $storyGroupPrivate,
            'showPrivates' => $model->canUserControlYou(),
        ]);
    }

    /**
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModelByKey(string $key): Story
    {
        $model = Story::findOne(['key' => $key]);

        if ($model === null) {
            throw new NotFoundHttpException(Yii::t('app', 'STORY_NOT_AVAILABLE'));
        }

        if (!in_array($model->visibility, Visibility::determineVisibilityVector($model->epic))) {
            throw new NotFoundHttpException(Yii::t('app', 'STORY_NOT_AVAILABLE'));
        }

        return $model;
    }

    /**
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModelById(string $id): Story
    {
        $model = Story::findOne(['story_id' => $id]);

        if ($model === null) {
            throw new NotFoundHttpException(Yii::t('app', 'STORY_NOT_AVAILABLE'));
        }

        if (!in_array($model->visibility, Visibility::determineVisibilityVector($model->epic))) {
            throw new NotFoundHttpException(Yii::t('app', 'STORY_NOT_AVAILABLE'));
        }

        return $model;
    }
}
