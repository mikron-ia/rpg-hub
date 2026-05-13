<?php

namespace backend\controllers;

use common\models\core\Visibility;
use common\models\Group;
use common\models\StoryGroupAssignment;
use common\models\type\AssignmentRank;
use Override;
use Throwable;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use yii\web\HttpException;
use yii\web\Response;

class StoryAssignmentGroupController extends AssignmentAbstractController
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
        $rank = Yii::$app->request->post('rank') ?? AssignmentRank::Other->value;
        $visibility = Yii::$app->request->post('visibility');

        $validVisibility = Visibility::tryFrom($visibility);
        if ($validVisibility === null) {
            throw new BadRequestHttpException(Yii::t('app', 'ERROR_VISIBILITY_NOT_VALID'));
        }

        $validRank = AssignmentRank::tryFrom($rank);
        if ($validRank === null) {
            throw new BadRequestHttpException(Yii::t('app', 'ERROR_ASSIGNMENT_RANK_NOT_VALID'));
        }

        $story = $this->findStory($storyKey);
        $groups = $this->findGroups($groupIds, $story->epic);

        $existingAssignments = StoryGroupAssignment::findAll([
            'story_id' => $story->story_id,
            'rank' => $validRank->value,
            'visibility' => $validVisibility->value,
        ]);

        $groupIdsToUnassign = array_diff(array_column($existingAssignments, 'group_id'), $groupIds);
        $groupIdsToSkip = array_intersect($groupIds, array_column($existingAssignments, 'group_id'));

        try {
            StoryGroupAssignment::deleteAll([
                'group_id' => $groupIdsToUnassign,
                'story_id' => $story->story_id,
                'rank' => $validRank->value,
                'visibility' => $validVisibility->value,
            ]);

            $unassignedGroups = $this->findGroups($groupIdsToUnassign, $story->epic);
            array_walk($unassignedGroups, function (Group $group) {
                $group->importancePack->flagForRecalculation();
            });

            foreach ($groups as $groupId => $group) {
                if (!in_array($groupId, $groupIdsToSkip)) {
                    StoryGroupAssignment::create($groupId, $story->story_id, $validVisibility, $validRank);
                    $group->importancePack->flagForRecalculation();
                }
            }
        } catch (Throwable $e) {
            return $this->respondWithError($e->getMessage());
        }

        return $this->respondWithSuccess();
    }
}
