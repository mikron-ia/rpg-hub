<?php

namespace common\models\type;

use Yii;

enum AssignmentType: string
{
    case Vital = 'vital';
    case Major = 'major';
    case Minor = 'minor';
    case Other = 'other';

    public function name(): string
    {
        return match ($this) {
            self::Vital => Yii::t('app', 'ASSIGNMENT_TYPE_VITAL'),
            self::Major => Yii::t('app', 'ASSIGNMENT_TYPE_MAJOR'),
            self::Minor => Yii::t('app', 'ASSIGNMENT_TYPE_MINOR'),
            self::Other => Yii::t('app', 'ASSIGNMENT_TYPE_OTHER'),
        };
    }
}
