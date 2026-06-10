<?php

namespace common\models\state;

use Yii;

enum ProjectStatus: string
{
    use StatusCommons;

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

    public function getAllowedSuccessors(): array
    {
        return match ($this) {
            self::Unknown => [self::Unknown]
        };
    }
}
