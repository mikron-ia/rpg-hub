<?php

namespace common\models\type;

use Yii;

enum ProjectType: string
{
    case None = 'none';               // No type set

    public function name(): string
    {
        return match ($this) {
            self::None => Yii::t('app', 'PROJECT_TYPE_NONE'),
        };
    }

    public function displayTag(): bool
    {
        return !($this === self::None);
    }

    /**
     * @return array<string>
     */
    public static function allowedCodes(): array
    {
        return array_map(fn(self $type) => $type->value, self::cases());
    }

    /**
     * @return array<string,string>
     */
    public static function namesForDropdown(): array
    {
        return array_reduce(
            self::cases(),
            static function (array $names, self $storyType): array {
                $names[$storyType->value] = $storyType->name();
                return $names;
            },
            []
        );
    }
}
