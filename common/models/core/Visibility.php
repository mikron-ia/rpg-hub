<?php

namespace common\models\core;

use Yii;

/**
 * Class Visibility
 * @package common\models\tools
 */
final class Visibility
{
    const VISIBILITY_NONE = 'none';
    const VISIBILITY_GM = 'gm';
    const VISIBILITY_DESIGNATED = 'designated';
    const VISIBILITY_LOGGED = 'logged';
    const VISIBILITY_FULL = 'full';

    public $visibility;

    /**
     * @return string[]
     */
    static public function visibilityNames():array
    {
        return [
            Visibility::VISIBILITY_NONE => Yii::t('app', 'VISIBILITY_NONE'),
            Visibility::VISIBILITY_GM => Yii::t('app', 'VISIBILITY_GM'),
            Visibility::VISIBILITY_DESIGNATED => Yii::t('app', 'VISIBILITY_DESIGNATED'),
            Visibility::VISIBILITY_LOGGED => Yii::t('app', 'VISIBILITY_LOGGED'),
            Visibility::VISIBILITY_FULL => Yii::t('app', 'VISIBILITY_FULL'),
        ];
    }

    /**
     * @return string[]
     */
    static public function visibilityNamesLowercase():array
    {
        return [
            Visibility::VISIBILITY_NONE => Yii::t('app', 'VISIBILITY_NONE_LOWERCASE'),
            Visibility::VISIBILITY_GM => Yii::t('app', 'VISIBILITY_GM_LOWERCASE'),
            Visibility::VISIBILITY_DESIGNATED => Yii::t('app', 'VISIBILITY_DESIGNATED_LOWERCASE'),
            Visibility::VISIBILITY_LOGGED => Yii::t('app', 'VISIBILITY_LOGGED_LOWERCASE'),
            Visibility::VISIBILITY_FULL => Yii::t('app', 'VISIBILITY_FULL_LOWERCASE'),
        ];
    }

    static public function allowedVisibilities():array
    {
        return array_keys(self::visibilityNames());
    }

    static public function create($code)
    {
        $visibility = new Visibility();
        $visibility->visibility = $code;
        return $visibility;
    }

    /**
     * @return string|null
     */
    public function getName()
    {
        $names = self::visibilityNames();
        return isset($names[$this->visibility]) ? $names[$this->visibility] : null;
    }

    /**
     * @return string|null
     */
    public function getNameLowercase()
    {
        $names = self::visibilityNamesLowercase();
        return isset($names[$this->visibility]) ? $names[$this->visibility] : null;
    }
}
