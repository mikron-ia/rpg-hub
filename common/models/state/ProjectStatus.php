<?php

namespace common\models\state;

use Yii;

enum ProjectStatus: string
{
    case Unknown = 'unknown';

    public function getName(): string
    {
        return match ($this) {
            self::Unknown => Yii::t('app', 'PROJECT_STATUS_UNKNOWN'),
        };
    }

    public function getClass(): string
    {
        return match ($this) {
            self::Unknown => 'project-status-unknown',
        };
    }

    /**
     * @return array<string>
     */
    public static function getAllowedCodes(): array
    {
        return array_map(fn(self $type) => $type->value, self::cases());
    }

    public function getAllowedSuccessors(): array
    {
        return match ($this) {
            self::Unknown => [self::Unknown]
        };
    }

    /**
     * @return array<string,string>
     */
    public function allowedSuccessorsAsStrings(): array
    {
        $allowed = [];
        foreach ($this->getAllowedSuccessors() as $successor) {
            $allowed[$successor->value] = $successor->getName();
        }

        return $allowed;
    }

    /**
     * @return array<string,string>
     */
    public static function namesForDropdown(): array
    {
        return array_reduce(
            self::cases(),
            static function (array $names, self $type): array {
                $names[$type->value] = $type->getName();
                return $names;
            },
            []
        );
    }
}
