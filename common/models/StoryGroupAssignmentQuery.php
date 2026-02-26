<?php

namespace common\models;

use common\models\core\Visibility;
use yii\db\ActiveQuery;
use yii\helpers\Html;

final class StoryGroupAssignmentQuery extends StoryGroupAssignment
{
    private static function getGroupAssignments(int $storyId, Visibility $visibility): ActiveQuery
    {
        return StoryGroupAssignment::find()->andWhere([
            'story_group_assignment.story_id' => $storyId,
            'story_group_assignment.visibility' => $visibility->value,
        ]);
    }

    private static function getStoryAssignments(int $groupId, Visibility $visibility): ActiveQuery
    {
        return StoryGroupAssignment::find()->andWhere([
            'story_group_assignment.group_id' => $groupId,
            'story_group_assignment.visibility' => $visibility->value,
        ]);
    }

    private static function getGroupAssignmentLinksForOperator(int $storyId, Visibility $visibility): array
    {
        $assignments = self::getGroupAssignments($storyId, $visibility)
            ->joinWith('group')
            ->orderBy('group.name ASC')
            ->all();

        return self::processIntoLinks($assignments, 'group');
    }

    private static function getStoryAssignmentLinksForOperator(int $groupId, Visibility $visibility): array
    {
        $assignments = self::getStoryAssignments($groupId, $visibility)
            ->joinWith('story')
            ->orderBy('story.position ASC')
            ->all();

        return self::processIntoLinks($assignments, 'story');
    }

    public static function getGroupAssignmentLinksForUser(int $storyId): array
    {
        $assignments = self::getGroupAssignments($storyId, Visibility::VISIBILITY_FULL)
            ->joinWith('group')
            ->andWhere(['group.visibility' => Visibility::VISIBILITY_FULL->value])
            ->orderBy('group.name ASC')
            ->all();

        return self::processIntoLinks($assignments, 'group');
    }

    public static function getStoryAssignmentLinksForUser(int $groupId): array
    {
        $assignments = self::getStoryAssignments($groupId, Visibility::VISIBILITY_FULL)
            ->joinWith('story')
            ->andWhere(['story.visibility' => Visibility::VISIBILITY_FULL->value])
            ->orderBy('story.position DESC')
            ->all();

        return self::processIntoLinks($assignments, 'story');
    }

    public static function getGroupAssignmentPublicLinksForOperator(int $storyId): array
    {
        return self::getGroupAssignmentLinksForOperator($storyId, Visibility::VISIBILITY_FULL);
    }

    public static function getStoryAssignmentPublicLinksForOperator(int $groupId): array
    {
        return self::getStoryAssignmentLinksForOperator($groupId, Visibility::VISIBILITY_FULL);
    }

    public static function getGroupAssignmentPrivateLinksForOperator(int $storyId): array
    {
        return self::getGroupAssignmentLinksForOperator($storyId, Visibility::VISIBILITY_GM);
    }

    public static function getStoryAssignmentPrivateLinksForOperator(int $groupId): array
    {
        return self::getStoryAssignmentLinksForOperator($groupId, Visibility::VISIBILITY_GM);
    }

    private static function processIntoLinks(array $assignments, string $address): array
    {
        return array_map(
            fn(StoryGroupAssignment $assignment) => Html::a(
                $assignment->{$address}->name,
                [$address . '/view', 'key' => $assignment->{$address}->key]
            ),
            $assignments
        );
    }
}
