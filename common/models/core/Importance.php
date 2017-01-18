<?php

namespace common\models\core;

use Yii;

/**
 * Class Importance
 * @package common\models\core
 */
final class Importance
{
    const IMPORTANCE_NONE = '4-none';
    const IMPORTANCE_LOW = '3-low';
    const IMPORTANCE_MEDIUM = '2-medium';
    const IMPORTANCE_HIGH = '1-high';
    const IMPORTANCE_EXTREME = '0-extreme';

    public $importance;

    /**
     * Provides importance names
     * @return string[]
     */
    static public function importanceNames():array
    {
        $names = [
            self::IMPORTANCE_NONE => Yii::t('app', 'IMPORTANCE_NONE'),
            self::IMPORTANCE_LOW => Yii::t('app', 'IMPORTANCE_LOW'),
            self::IMPORTANCE_MEDIUM => Yii::t('app', 'IMPORTANCE_MEDIUM'),
            self::IMPORTANCE_HIGH => Yii::t('app', 'IMPORTANCE_HIGH'),
            self::IMPORTANCE_EXTREME => Yii::t('app', 'IMPORTANCE_EXTREME'),
        ];

        $allowed = self::allowedImportance();

        foreach ($names as $key => $name) {
            if (!in_array($key, $allowed)) {
                unset($names[$key]);
            }
        }

        return $names;
    }

    /**
     * Provides importance names in lowercase
     * @return string[]
     */
    static public function importanceNamesLowercase():array
    {
        return [
            self::IMPORTANCE_NONE => Yii::t('app', 'IMPORTANCE_NONE_LOWERCASE'),
            self::IMPORTANCE_LOW => Yii::t('app', 'IMPORTANCE_LOW_LOWERCASE'),
            self::IMPORTANCE_MEDIUM => Yii::t('app', 'IMPORTANCE_MEDIUM_LOWERCASE'),
            self::IMPORTANCE_HIGH => Yii::t('app', 'IMPORTANCE_HIGH_LOWERCASE'),
            self::IMPORTANCE_EXTREME => Yii::t('app', 'IMPORTANCE_EXTREME_LOWERCASE'),
        ];
    }

    /**
     * Lists allowed importance
     * @return string[]
     */
    static public function allowedImportance():array
    {
        return [
            self::IMPORTANCE_NONE,
            self::IMPORTANCE_LOW,
            self::IMPORTANCE_MEDIUM,
            self::IMPORTANCE_HIGH,
            self::IMPORTANCE_EXTREME
        ];
    }

    /**
     * @param $code
     * @return Importance
     */
    static public function create($code):Importance
    {
        $importance = new Importance();
        $importance->importance = $code;
        return $importance;
    }

    /**
     * Provides importance name
     * @return string|null
     */
    public function getName()
    {
        $names = self::importanceNames();
        return isset($names[$this->importance]) ? $names[$this->importance] : null;
    }

    /**
     * Provides importance name in lowercase
     * @return string|null
     */
    public function getNameLowercase()
    {
        $names = self::importanceNamesLowercase();
        return isset($names[$this->importance]) ? $names[$this->importance] : null;
    }
}
