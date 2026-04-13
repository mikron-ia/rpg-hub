<?php

namespace backend\controllers;

use common\components\EpicAssistance;
use common\models\core\Visibility;
use common\models\Epic;
use common\models\Group;
use common\models\StoryGroupAssignment;
use Override;
use Throwable;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class StoryAssignmentGroupController extends StoryAssignmentAbstractController
{
    #[Override]
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => [
                            'get-story-groups',
                            'set-story-groups',
                        ],
                        'allow' => true,
                        'roles' => ['operator'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'set-story-groups' => ['PUT'],
                ],
            ],
        ];
    }

    /**
     * @throws HttpException
     */
    public function actionGetStoryGroups(string $storyKey): string
    {
        $model = $this->findStory($storyKey);
        $this->checkAccess($model);

        $query = StoryGroupAssignment::find()
            ->where(['story_id' => $model->story_id])
            ->joinWith('group')
            ->orderBy('name ASC');

        return $this->renderAjax('_view_group_list', [
            'dataProvider' => new ActiveDataProvider([
                'query' => $query,
                'pagination' => false,
            ]),
        ]);
    }

    /**
     * @throws HttpException
     */
    public function actionSetStoryGroups(): Response
    {
        $groupIds = Yii::$app->request->post('keys');
        $storyKey = Yii::$app->request->post('storyKey');
        $visibility = Yii::$app->request->post('visibility');

        $validateVisibility = Visibility::tryFrom($visibility);

        if ($validateVisibility === null) {
            throw new BadRequestHttpException(Yii::t('app', 'ERROR_VISIBILITY_NOT_VALID'));
        }

        $story = $this->findStory($storyKey);
        $groups = $this->findGroups($groupIds, $story->epic);

        try {
            StoryGroupAssignment::deleteAll([
                'story_id' => $story->story_id,
                'visibility' => $validateVisibility->value,
            ]);

            foreach ($groups as $groupId => $group) {
                $assignment = new StoryGroupAssignment();
                $assignment->group_id = $groupId;
                $assignment->story_id = $story->story_id;
                $assignment->visibility = $validateVisibility->value;
                $assignment->save();
            }
        } catch (Throwable $e) {
            return new Response(['statusCode' => 500, 'content' => $e->getMessage()]);
        }

        return new Response(['statusCode' => 200]);
    }

    /**
     * @throws HttpException
     */
    protected function findGroup(string $key): Group
    {
        $model = Group::findOne(['key' => $key]);

        if ($model === null) {
            throw new NotFoundHttpException(Yii::t('app', 'GROUP_NOT_AVAILABLE'));
        }

        $this->checkAccess($model);

        $this->selectEpic($model->epic->key, $model->epic_id, $model->epic->name);

        return $model;
    }

    /**
     * @param array<int> $ids
     *
     * @return array<int,Group>
     *
     * @throws HttpException
     */
    protected function findGroups(array $ids, Epic $epic): array
    {
        $models = Group::findAll($ids);

        if ($models === null) {
            return [];
        }

        $indexedModels = [];
        foreach ($models as $model) {
            $this->checkAccess($model);
            $this->checkEpicConsistency($model, $epic);
            $indexedModels[$model->group_id] = $model;
        }

        return $indexedModels;
    }
}
