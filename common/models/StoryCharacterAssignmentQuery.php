<?php

namespace common\models;

use common\models\core\Visibility;
use yii\db\ActiveQuery;
use yii\helpers\Html;

final class StoryCharacterAssignmentQuery extends StoryCharacterAssignment
{
    private static function getCharacterAssignments(int $storyId, Visibility $visibility): ActiveQuery
    {
        return StoryCharacterAssignment::find()->andWhere([
            'story_character_assignment.story_id' => $storyId,
            'story_character_assignment.visibility' => $visibility->value,
        ]);
    }

    private static function getStoryAssignments(int $characterId, Visibility $visibility): ActiveQuery
    {
        return StoryCharacterAssignment::find()->andWhere([
            'story_character_assignment.character_id' => $characterId,
            'story_character_assignment.visibility' => $visibility->value,
        ]);
    }

    private static function getCharacterAssignmentLinksForOperator(int $storyId, Visibility $visibility): array
    {
        $assignments = self::getCharacterAssignments($storyId, $visibility)->joinWith('character')
            ->orderBy('character.name ASC')
            ->all();

        return self::processIntoLinks($assignments, 'character');
    }

    private static function getStoryAssignmentLinksForOperator(int $characterId, Visibility $visibility): array
    {
        $assignments = self::getStoryAssignments($characterId, $visibility)
            ->joinWith('story')
            ->orderBy('story.position ASC')
            ->all();

        return self::processIntoLinks($assignments, 'story');
    }

    private static function getCharacterAssignmentLinksForUser(int $storyId): array
    {
        $assignments = self::getCharacterAssignments($storyId, Visibility::VISIBILITY_FULL)
            ->joinWith('character')
            ->andWhere(['character.visibility' => Visibility::VISIBILITY_FULL->value])
            ->orderBy('character.name ASC')
            ->all();

        return self::processIntoLinks($assignments, 'character');
    }

    private static function getStoryAssignmentLinksForUser(int $characterId): array
    {
        $assignments = self::getStoryAssignments($characterId, Visibility::VISIBILITY_FULL)
            ->joinWith('story')
            ->andWhere(['story.visibility' => Visibility::VISIBILITY_FULL->value])
            ->orderBy('character.name ASC')
            ->all();

        return self::processIntoLinks($assignments, 'story');
    }

    public static function getCharacterAssignmentPublicLinksForOperator(int $storyId): array
    {
        return self::getCharacterAssignmentLinksForOperator($storyId, Visibility::VISIBILITY_FULL);
    }

    public static function getStoryAssignmentPublicLinksForOperator(int $characterId): array
    {
        return self::getStoryAssignmentLinksForOperator($characterId, Visibility::VISIBILITY_FULL);
    }

    public static function getCharacterAssignmentPrivateLinksForOperator(int $storyId): array
    {
        return self::getCharacterAssignmentLinksForOperator($storyId, Visibility::VISIBILITY_GM);
    }

    public static function getStoryAssignmentPrivateLinksForOperator(int $characterId): array
    {
        return self::getStoryAssignmentLinksForOperator($characterId, Visibility::VISIBILITY_GM);
    }

    private static function processIntoLinks(array $assignments, string $address): array
    {
        return array_map(
            fn(StoryCharacterAssignment $assignment) => Html::a(
                $assignment->{$address}->name,
                [$address . '/view', 'key' => $assignment->{$address}->key]
            ),
            $assignments
        );
    }
}
