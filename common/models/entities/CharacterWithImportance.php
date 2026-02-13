<?php

namespace common\models\entities;

use common\models\Character;

class CharacterWithImportance
{
    public const USER_NAME_PATTERN = 'user_id_%d';

    public readonly int $character_id;
    public readonly int $epic_id;
    public readonly string $key;
    public readonly string $name;
    public readonly string $tagline;
    public readonly string $visibility;
    public readonly string $importance_category;

    /**
     * @var array<string>
     */
    private array $importances = [];

    public function __construct(Character $character)
    {
        $this->character_id = $character->character_id;
        $this->epic_id = $character->epic_id;
        $this->key = $character->key;
        $this->name = $character->name;
        $this->tagline = $character->tagline;
        $this->visibility = $character->getVisibility()->getName();
        $this->importance_category = $character->getImportanceCategory();
    }

    public function setImportance(int $userId, int $importance): void
    {
        $this->importances[sprintf(self::USER_NAME_PATTERN, $userId)] = $importance;
    }

    /**
     * @return array<string>
     */
    public function getImportanceFieldKeys(): array
    {
        return array_keys($this->importances);
    }

    /**
     * Designed to get importance for a specific user
     * Done this way because Yii widgets work best when properties are public
     */
    public function __get(string $name): ?int
    {
        return $this->importances[$name] ?? null;
    }
}
