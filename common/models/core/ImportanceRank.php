<?php

namespace common\models\core;

use Yii;

/**
 * Class ImportanceRank
 * @package common\models\core
 */
final class ImportanceRank
{
    const IMPORTANCE_RANK_EXTREME_LOW = 'extreme-low';
    const IMPORTANCE_RANK_LOW = 'low';
    const IMPORTANCE_RANK_MEDIUM_LOW = 'medium-low';
    const IMPORTANCE_RANK_MEDIUM = 'medium';
    const IMPORTANCE_RANK_MEDIUM_HIGH = 'medium-high';
    const IMPORTANCE_RANK_HIGH = 'high';
    const IMPORTANCE_RANK_EXTREMELY_HIGH = 'extreme-high';

    const IMPORTANCE_RANK_INCORRECT = 'incorrect';
    const IMPORTANCE_RANK_UNKNOWN = 'unknown';

    const IMPORTANCE_RANK_CODES = [
        self::IMPORTANCE_RANK_EXTREME_LOW,
        self::IMPORTANCE_RANK_LOW,
        self::IMPORTANCE_RANK_MEDIUM_LOW,
        self::IMPORTANCE_RANK_MEDIUM,
        self::IMPORTANCE_RANK_MEDIUM_HIGH,
        self::IMPORTANCE_RANK_HIGH,
        self::IMPORTANCE_RANK_EXTREMELY_HIGH,
        self::IMPORTANCE_RANK_UNKNOWN,
    ];

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
            self::IMPORTANCE_RANK_EXTREME_LOW => Yii::t('app', 'IMPORTANCE_RANK_EXTREME_LOW'),
            self::IMPORTANCE_RANK_LOW => Yii::t('app', 'IMPORTANCE_RANK_LOW'),
            self::IMPORTANCE_RANK_MEDIUM_LOW => Yii::t('app', 'IMPORTANCE_RANK_MEDIUM_LOW'),
            self::IMPORTANCE_RANK_MEDIUM => Yii::t('app', 'IMPORTANCE_RANK_MEDIUM'),
            self::IMPORTANCE_RANK_MEDIUM_HIGH => Yii::t('app', 'IMPORTANCE_RANK_MEDIUM_HIGH'),
            self::IMPORTANCE_RANK_HIGH => Yii::t('app', 'IMPORTANCE_RANK_HIGH'),
            self::IMPORTANCE_RANK_EXTREMELY_HIGH => Yii::t('app', 'IMPORTANCE_RANK_EXTREMELY_HIGH'),
            self::IMPORTANCE_RANK_UNKNOWN => Yii::t('app', 'IMPORTANCE_RANK_UNKNOWN'),
            self::IMPORTANCE_RANK_INCORRECT => Yii::t('app', 'IMPORTANCE_RANK_INCORRECT'),
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
            self::IMPORTANCE_RANK_EXTREME_LOW => Yii::t('app', 'IMPORTANCE_RANK_EXTREME_LOW_LOWERCASE'),
            self::IMPORTANCE_RANK_LOW => Yii::t('app', 'IMPORTANCE_RANK_LOW_LOWERCASE'),
            self::IMPORTANCE_RANK_MEDIUM_LOW => Yii::t('app', 'IMPORTANCE_RANK_MEDIUM_LOW_LOWERCASE'),
            self::IMPORTANCE_RANK_MEDIUM => Yii::t('app', 'IMPORTANCE_RANK_MEDIUM_LOWERCASE'),
            self::IMPORTANCE_RANK_MEDIUM_HIGH => Yii::t('app', 'IMPORTANCE_RANK_MEDIUM_HIGH_LOWERCASE'),
            self::IMPORTANCE_RANK_HIGH => Yii::t('app', 'IMPORTANCE_RANK_HIGH_LOWERCASE'),
            self::IMPORTANCE_RANK_EXTREMELY_HIGH => Yii::t('app', 'IMPORTANCE_RANK_EXTREMELY_HIGH_LOWERCASE'),
            self::IMPORTANCE_RANK_UNKNOWN => Yii::t('app', 'IMPORTANCE_RANK_UNKNOWN_LOWERCASE'),
            self::IMPORTANCE_RANK_INCORRECT => Yii::t('app', 'IMPORTANCE_RANK_INCORRECT_LOWERCASE'),
        ];
    }

    /**
     * Creates ImportanceRank object from string code
     * @param $code
     * @return ImportanceRank
     */
    static public function create($code): ImportanceRank
    {
        $importance = new ImportanceRank();

        if (!in_array($code, self::IMPORTANCE_RANK_CODES)) {
            $code = self::IMPORTANCE_RANK_INCORRECT;
        }

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
