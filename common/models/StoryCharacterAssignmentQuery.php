<?php

namespace common\models;

use common\dto\LinkWithVisibility;
use common\models\core\Visibility;
use yii\db\ActiveQuery;
use yii\helpers\Url;

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

    public static function getCharacterAssignmentLinksForUser(int $storyId): array
    {
        $assignments = self::getCharacterAssignments($storyId, Visibility::Full)
            ->joinWith('character')
            ->andWhere(['character.visibility' => Visibility::Full->value])
            ->orderBy('character.name ASC')
            ->all();

        return self::processIntoLinks($assignments, 'character');
    }

    public static function getStoryAssignmentLinksForUser(int $characterId): array
    {
        $assignments = self::getStoryAssignments($characterId, Visibility::Full)
            ->joinWith('story')
            ->andWhere(['story.visibility' => Visibility::Full->value])
            ->orderBy('story.position DESC')
            ->all();

        return self::processIntoLinks($assignments, 'story');
    }

    public static function getCharacterAssignmentPublicLinksForOperator(int $storyId): array
    {
        return self::getCharacterAssignmentLinksForOperator($storyId, Visibility::Full);
    }

    public static function getStoryAssignmentPublicLinksForOperator(int $characterId): array
    {
        return self::getStoryAssignmentLinksForOperator($characterId, Visibility::Full);
    }

    public static function getCharacterAssignmentPrivateLinksForOperator(int $storyId): array
    {
        return self::getCharacterAssignmentLinksForOperator($storyId, Visibility::GameMaster);
    }

    public static function getStoryAssignmentPrivateLinksForOperator(int $characterId): array
    {
        return self::getStoryAssignmentLinksForOperator($characterId, Visibility::GameMaster);
    }

    private static function processIntoLinks(array $assignments, string $address): array
    {
        return array_map(
            fn(StoryCharacterAssignment $assignment) => new LinkWithVisibility(
                text: $assignment->{$address}->name . ' (' . strtolower($assignment->getRank()->getNameForBrackets()) . ')',
                url: Url::to([$address . '/view', 'key' => $assignment->{$address}->key]),
                isSecret: $assignment->{$address}->visibility === Visibility::GameMaster->value,
            ),
            $assignments
        );
    }
}
