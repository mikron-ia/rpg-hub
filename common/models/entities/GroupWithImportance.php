<?php

namespace common\models\entities;

use common\models\Group;

class GroupWithImportance
{
    public const string USER_NAME_PATTERN = 'user_id_%d';

    public readonly int $group_id;
    public readonly int $epic_id;
    public readonly string $key;
    public readonly string $name;
    public readonly string $visibility;
    public readonly string $importance_category;

    /**
     * @var array<string>
     */
    private array $importances = [];

    public function __construct(Group $group)
    {
        $this->group_id = $group->group_id;
        $this->epic_id = $group->epic_id;
        $this->key = $group->key;
        $this->name = $group->name;
        $this->visibility = $group->getVisibility()->getName();
        $this->importance_category = $group->getImportanceCategory();
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
