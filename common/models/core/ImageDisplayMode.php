<?php

namespace common\models\core;

use Yii;

enum ImageDisplayMode: string
{
    case Always = 'always';
    case Backup = 'backup';
    case Never = 'never';

    public function getName(): string
    {
        return match ($this) {
            self::Always => Yii::t('app', 'IMAGE_DISPLAY_MODE_ALWAYS'),
            self::Backup => Yii::t('app', 'IMAGE_DISPLAY_MODE_BACKUP'),
            self::Never => Yii::t('app', 'IMAGE_DISPLAY_MODE_NEVER'),
        };
    }

    /**
     * @return array<string>
     */
    public static function allowedValues(): array
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
            static function (array $names, self $mode): array {
                $names[$mode->value] = $mode->getName();
                return $names;
            },
            []
        );
    }
}
