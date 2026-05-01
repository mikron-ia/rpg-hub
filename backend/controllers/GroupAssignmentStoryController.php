<?php

namespace backend\controllers;

use common\models\core\Visibility;
use common\models\StoryGroupAssignment;
use Override;
use Throwable;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use yii\web\HttpException;
use yii\web\Response;

class GroupAssignmentStoryController extends AssignmentAbstractController
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
                            'get-group-stories',
                            'set-group-stories',
                        ],
                        'allow' => true,
                        'roles' => ['operator'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'set-group-stories' => ['PUT'],
                ],
            ],
        ];
    }

    /**
     * @throws HttpException
     */
    public function actionGetGroupStories(string $groupKey): string
    {
        $model = $this->findGroup($groupKey);
        $this->checkAccess($model);

        $query = StoryGroupAssignment::find()
            ->where(['story_group_assignment.group_id' => $model->group_id])
            ->joinWith('story')
            ->orderBy('name ASC');

        return $this->renderAjax('_view_story_list', [
            'dataProvider' => new ActiveDataProvider([
                'query' => $query,
                'pagination' => false,
            ]),
        ]);
    }

    /**
     * @throws HttpException
     */
    public function actionSetGroupStories(): Response
    {
        $storyIds = Yii::$app->request->post('keys', []);
        $groupKey = Yii::$app->request->post('groupKey');
        $visibility = Yii::$app->request->post('visibility');

        $validatedVisibility = Visibility::tryFrom($visibility);

        if ($validatedVisibility === null) {
            throw new BadRequestHttpException(Yii::t('app', 'ERROR_VISIBILITY_NOT_VALID'));
        }

        $group = $this->findGroup($groupKey);
        $stories = $this->findStories($storyIds, $group->epic);

        $existingAssignments = StoryGroupAssignment::findAll([
            'group_id' => $group->group_id,
            'visibility' => $validatedVisibility->value,
        ]);

        $storyIdsToUnassign = array_diff(array_column($existingAssignments, 'story_id'), $storyIds);
        $storyIdsToSkip = array_intersect($storyIds, array_column($existingAssignments, 'story_id'));

        try {
            StoryGroupAssignment::deleteAll([
                'group_id' => $group->group_id,
                'story_id' => $storyIdsToUnassign,
                'visibility' => $validatedVisibility->value,
            ]);

            foreach ($stories as $storyId => $story) {
                if (!in_array($storyId, $storyIdsToSkip)) {
                    StoryGroupAssignment::create($group->group_id, $storyId, $validatedVisibility);
                }
            }

            $group->importancePack->flagForRecalculation();
        } catch (Throwable $e) {
            return $this->respondWithError($e->getMessage());
        }

        return $this->respondWithSuccess();
    }
}
