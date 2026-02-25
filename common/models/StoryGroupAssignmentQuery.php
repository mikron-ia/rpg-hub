<?php

namespace common\models;

use common\models\core\Visibility;
use yii\db\ActiveQuery;
use yii\helpers\Html;

class StoryGroupAssignmentQuery extends StoryGroupAssignment
{
    private static function getGroupAssignments(int $storyId, Visibility $visibility): ActiveQuery
    {
        return StoryGroupAssignment::find()->andWhere([
            'story_group_assignment.story_id' => $storyId,
            'story_group_assignment.visibility' => $visibility->value,
        ]);
    }

    public static function getGroupAssignmentLinksForOperator(int $storyId, Visibility $visibility): array
    {
        $assignments = self::getGroupAssignments($storyId, $visibility)->joinWith('group')
            ->orderBy('group.name ASC')
            ->all();

        return array_map(
            fn(StoryGroupAssignment $assignment) => Html::a(
                $assignment->group->name,
                ['group/view', 'key' => $assignment->group->key]
            ),
            $assignments
        );
    }
}