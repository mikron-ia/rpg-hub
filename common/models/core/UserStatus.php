<?php

namespace common\models\core;

use Yii;

enum UserStatus: int
{
    case Forgotten = 0; // deleted and forgotten, data erased from current database
    case Deleted = 1; // detached from everything and deleted, archival data retained
    case Disabled = 5; // deactivated, cannot log in, can be reactivated
    case Active = 10; // fully active, fully visible

    public function getName(): string
    {
        return match ($this) {
            self::Forgotten => Yii::t('app', 'USER_STATUS_FORGOTTEN'),
            self::Deleted => Yii::t('app', 'USER_STATUS_DELETED'),
            self::Disabled => Yii::t('app', 'USER_STATUS_DISABLED'),
            self::Active => Yii::t('app', 'USER_STATUS_ACTIVE'),
        };
    }
}
