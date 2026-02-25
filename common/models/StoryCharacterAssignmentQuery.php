<?php

namespace common\models;

use common\models\core\Visibility;
use yii\db\ActiveQuery;
use yii\helpers\Html;

class StoryCharacterAssignmentQuery extends StoryCharacterAssignment
{
    private static function getCharacterAssignments(int $storyId, Visibility $visibility): ActiveQuery
    {
        return StoryCharacterAssignment::find()->andWhere([
            'story_character_assignment.story_id' => $storyId,
            'story_character_assignment.visibility' => $visibility->value,
        ]);
    }

    public static function getCharacterAssignmentLinksForOperator(int $storyId, Visibility $visibility): array
    {
        $assignments = self::getCharacterAssignments($storyId, $visibility)->joinWith('character')
            ->orderBy('character.name ASC')
            ->all();

        return array_map(
            fn(StoryCharacterAssignment $assignment) => Html::a(
                $assignment->character->name,
                ['character/view', 'key' => $assignment->character->key]
            ),
            $assignments
        );
    }
}
