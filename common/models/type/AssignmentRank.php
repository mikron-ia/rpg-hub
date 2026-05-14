<?php

namespace common\models\type;

use Yii;

enum AssignmentRank: string
{
    case Vital = 'vital';
    case Major = 'major';
    case Minor = 'minor';
    case Other = 'other';

    public function getName(): string
    {
        return match ($this) {
            self::Vital => Yii::t('app', 'ASSIGNMENT_RANK_VITAL'),
            self::Major => Yii::t('app', 'ASSIGNMENT_RANK_MAJOR'),
            self::Minor => Yii::t('app', 'ASSIGNMENT_RANK_MINOR'),
            self::Other => Yii::t('app', 'ASSIGNMENT_RANK_OTHER'),
        };
    }

    public function getNameLowercase(): string
    {
        return strtolower($this->getName());
    }

    public function getNameForBrackets(): string
    {
        return Yii::t(
            'app',
            'ASSIGNMENT_RANK_NAME_FOR_BRACKETS {rank}',
            ['rank' => $this->getNameLowercase()]
        );
    }
}
