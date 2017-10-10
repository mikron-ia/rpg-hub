<?php

namespace common\models\core;

use Yii;

/**
 * Class ImportanceRank
 * @package common\models\core
 */
final class ImportanceRank
{
    const IMPORTANCE_RANK_LOWEST = 'lowest';
    const IMPORTANCE_RANK_LOW = 'low';
    const IMPORTANCE_RANK_MEDIUM = 'medium';
    const IMPORTANCE_RANK_HIGH = 'high';
    const IMPORTANCE_RANK_HIGHEST = 'extreme';

    /**
     * @var string
     */
    public $importance;

    /**
     * Provides importance names
     * @return string[]
     */
    static public function importanceNames(): array
    {
        $names = [
            self::IMPORTANCE_RANK_LOWEST => Yii::t('app', 'IMPORTANCE_RANK_LOWEST'),
            self::IMPORTANCE_RANK_LOW => Yii::t('app', 'IMPORTANCE_RANK_LOW_LOWERCASE'),
            self::IMPORTANCE_RANK_MEDIUM => Yii::t('app', 'IMPORTANCE_RANK_MEDIUM_LOWERCASE'),
            self::IMPORTANCE_RANK_HIGH => Yii::t('app', 'IMPORTANCE_RANK_HIGH_LOWERCASE'),
            self::IMPORTANCE_RANK_HIGHEST => Yii::t('app', 'IMPORTANCE_RANK_HIGHEST_LOWERCASE'),
        ];

        return $names;
    }

    /**
     * Provides importance names in lowercase
     * @return string[]
     */
    static public function importanceNamesLowercase(): array
    {
        return [
            self::IMPORTANCE_RANK_LOWEST => Yii::t('app', 'IMPORTANCE_RANK_LOWEST_LOWERCASE'),
            self::IMPORTANCE_RANK_LOW => Yii::t('app', 'IMPORTANCE_RANK_LOW_LOWERCASE'),
            self::IMPORTANCE_RANK_MEDIUM => Yii::t('app', 'IMPORTANCE_RANK_MEDIUM_LOWERCASE'),
            self::IMPORTANCE_RANK_HIGH => Yii::t('app', 'IMPORTANCE_RANK_HIGH_LOWERCASE'),
            self::IMPORTANCE_RANK_HIGHEST => Yii::t('app', 'IMPORTANCE_RANK_HIGHEST_LOWERCASE'),
        ];
    }

    /**
     * @param $code
     * @return ImportanceRank
     */
    static public function create($code): ImportanceRank
    {
        $importance = new ImportanceRank();
        $importance->importance = $code;
        return $importance;
    }

    /**
     * Provides importance name
     * @return string
     */
    public function getName(): string
    {
        $names = self::importanceNames();
        return isset($names[$this->importance]) ? $names[$this->importance] : '?';
    }

    /**
     * Provides importance name in lowercase
     * @return string
     */
    public function getNameLowercase(): string
    {
        $names = self::importanceNamesLowercase();
        return isset($names[$this->importance]) ? $names[$this->importance] : '?';
    }
}
