<?php

namespace common\models\tools;

use Yii;

/**
 * Class Visibility
 * @package common\models\tools
 */
class Visibility
{
    const VISIBILITY_NONE = 'none';
    const VISIBILITY_GM = 'gm';
    const VISIBILITY_DESIGNATED = 'designated';
    const VISIBILITY_LOGGED = 'logged';
    const VISIBILITY_FULL = 'full';

    /**
     * @return string[]
     */
    static public function visibilityNames()
    {
        return [
            Visibility::VISIBILITY_NONE => Yii::t('app', 'VISIBILITY_NONE'),
            Visibility::VISIBILITY_GM => Yii::t('app', 'VISIBILITY_GM'),
            Visibility::VISIBILITY_DESIGNATED => Yii::t('app', 'VISIBILITY_DESIGNATED'),
            Visibility::VISIBILITY_LOGGED => Yii::t('app', 'VISIBILITY_LOGGED'),
            Visibility::VISIBILITY_FULL => Yii::t('app', 'VISIBILITY_FULL'),
        ];
    }

    static public function allowedVisibilities()
    {
        return array_keys(self::visibilityNames());
    }
}
