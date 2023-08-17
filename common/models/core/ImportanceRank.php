<?php

namespace common\models\core;

use Yii;

/**
 * Enumeration ImportanceRank
 *
 * @package common\models\core
 */
enum ImportanceRank: string
{
    case IMPORTANCE_RANK_EXTREME_LOW = 'extreme-low';
    case IMPORTANCE_RANK_LOW = 'low';
    case IMPORTANCE_RANK_MEDIUM_LOW = 'medium-low';
    case IMPORTANCE_RANK_MEDIUM = 'medium';
    case IMPORTANCE_RANK_MEDIUM_HIGH = 'medium-high';
    case IMPORTANCE_RANK_HIGH = 'high';
    case IMPORTANCE_RANK_EXTREMELY_HIGH = 'extreme-high';

    case IMPORTANCE_RANK_INCORRECT = 'incorrect';
    case IMPORTANCE_RANK_UNKNOWN = 'unknown';

    /**
     * Provides importance name
     *
     * @return string
     */
    public function getName(): string
    {
        return match ($this) {
            self::IMPORTANCE_RANK_EXTREME_LOW => Yii::t('app', 'IMPORTANCE_RANK_EXTREME_LOW'),
            self::IMPORTANCE_RANK_LOW => Yii::t('app', 'IMPORTANCE_RANK_LOW'),
            self::IMPORTANCE_RANK_MEDIUM_LOW => Yii::t('app', 'IMPORTANCE_RANK_MEDIUM_LOW'),
            self::IMPORTANCE_RANK_MEDIUM => Yii::t('app', 'IMPORTANCE_RANK_MEDIUM'),
            self::IMPORTANCE_RANK_MEDIUM_HIGH => Yii::t('app', 'IMPORTANCE_RANK_MEDIUM_HIGH'),
            self::IMPORTANCE_RANK_HIGH => Yii::t('app', 'IMPORTANCE_RANK_HIGH'),
            self::IMPORTANCE_RANK_EXTREMELY_HIGH => Yii::t('app', 'IMPORTANCE_RANK_EXTREMELY_HIGH'),
            self::IMPORTANCE_RANK_UNKNOWN => Yii::t('app', 'IMPORTANCE_RANK_UNKNOWN'),
            self::IMPORTANCE_RANK_INCORRECT => Yii::t('app', 'IMPORTANCE_RANK_INCORRECT'),
        };
    }

    /**
     * Provides importance names in lowercase
     *
     * @return string
     */
    public function getNameLowercase(): string
    {
        return match ($this) {
            self::IMPORTANCE_RANK_EXTREME_LOW => Yii::t('app', 'IMPORTANCE_RANK_EXTREME_LOW_LOWERCASE'),
            self::IMPORTANCE_RANK_LOW => Yii::t('app', 'IMPORTANCE_RANK_LOW_LOWERCASE'),
            self::IMPORTANCE_RANK_MEDIUM_LOW => Yii::t('app', 'IMPORTANCE_RANK_MEDIUM_LOW_LOWERCASE'),
            self::IMPORTANCE_RANK_MEDIUM => Yii::t('app', 'IMPORTANCE_RANK_MEDIUM_LOWERCASE'),
            self::IMPORTANCE_RANK_MEDIUM_HIGH => Yii::t('app', 'IMPORTANCE_RANK_MEDIUM_HIGH_LOWERCASE'),
            self::IMPORTANCE_RANK_HIGH => Yii::t('app', 'IMPORTANCE_RANK_HIGH_LOWERCASE'),
            self::IMPORTANCE_RANK_EXTREMELY_HIGH => Yii::t('app', 'IMPORTANCE_RANK_EXTREMELY_HIGH_LOWERCASE'),
            self::IMPORTANCE_RANK_UNKNOWN => Yii::t('app', 'IMPORTANCE_RANK_UNKNOWN_LOWERCASE'),
            self::IMPORTANCE_RANK_INCORRECT => Yii::t('app', 'IMPORTANCE_RANK_INCORRECT_LOWERCASE'),
        };
    }
}
