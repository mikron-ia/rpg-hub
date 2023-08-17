<?php

namespace common\models\core;

use Yii;

/**
 * Enumeration ImportanceCategory
 *
 * @package common\models\core
 */
enum ImportanceCategory: string
{
    case IMPORTANCE_NONE = '4-none';
    case IMPORTANCE_LOW = '3-low';
    case IMPORTANCE_MEDIUM = '2-medium';
    case IMPORTANCE_HIGH = '1-high';
    case IMPORTANCE_EXTREME = '0-extreme';

    public function getName(): string
    {
        return match ($this) {
            self::IMPORTANCE_NONE => Yii::t('app', 'IMPORTANCE_NONE'),
            self::IMPORTANCE_LOW => Yii::t('app', 'IMPORTANCE_LOW'),
            self::IMPORTANCE_MEDIUM => Yii::t('app', 'IMPORTANCE_MEDIUM'),
            self::IMPORTANCE_HIGH => Yii::t('app', 'IMPORTANCE_HIGH'),
            self::IMPORTANCE_EXTREME => Yii::t('app', 'IMPORTANCE_EXTREME'),
        };
    }

    public function getNameWithDescription(): string
    {
        return $this->getName() . ' (' . $this->getDescription() . ')';
    }

    /**
     * Provides importance names
     *
     * @param bool $includeDescriptions
     *
     * @return string[]
     */
    static public function importanceNames(bool $includeDescriptions = false): array
    {
        $names = [];

        foreach (ImportanceCategory::cases() as $case) {
            if (in_array($case, self::allowedImportance())) {
                $names[$case->value] = $includeDescriptions ? $case->getNameWithDescription() : $case->getName();
            }
        }

        return $names;
    }

    /**
     * Lists of allowed importance types
     *
     * @return string[]
     */
    static private function allowedImportance(): array
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
     * Provides importance names in lowercase
     *
     * @return string
     */
    public function getNameLowercase(): string
    {
        return match ($this) {
            self::IMPORTANCE_NONE => Yii::t('app', 'IMPORTANCE_NONE_LOWERCASE'),
            self::IMPORTANCE_LOW => Yii::t('app', 'IMPORTANCE_LOW_LOWERCASE'),
            self::IMPORTANCE_MEDIUM => Yii::t('app', 'IMPORTANCE_MEDIUM_LOWERCASE'),
            self::IMPORTANCE_HIGH => Yii::t('app', 'IMPORTANCE_HIGH_LOWERCASE'),
            self::IMPORTANCE_EXTREME => Yii::t('app', 'IMPORTANCE_EXTREME_LOWERCASE'),
        };
    }

    /**
     * Provides importance names' descriptions
     *
     * @return string
     */
    public function getDescription(): string
    {
        return match ($this) {
            self::IMPORTANCE_NONE => Yii::t('app', 'IMPORTANCE_NONE_DESCRIPTION'),
            self::IMPORTANCE_LOW => Yii::t('app', 'IMPORTANCE_LOW_DESCRIPTION'),
            self::IMPORTANCE_MEDIUM => Yii::t('app', 'IMPORTANCE_MEDIUM_DESCRIPTION'),
            self::IMPORTANCE_HIGH => Yii::t('app', 'IMPORTANCE_HIGH_DESCRIPTION'),
            self::IMPORTANCE_EXTREME => Yii::t('app', 'IMPORTANCE_EXTREME_DESCRIPTION'),
        };
    }

    public function minimum(): int
    {
        return match ($this) {
            self::IMPORTANCE_NONE => 1,
            self::IMPORTANCE_LOW => 1,
            self::IMPORTANCE_MEDIUM => 2,
            self::IMPORTANCE_HIGH => 3,
            self::IMPORTANCE_EXTREME => 5,
        };
    }

    public function maximum(): int
    {
        return match ($this) {
            self::IMPORTANCE_NONE => 3,
            self::IMPORTANCE_LOW => 5,
            self::IMPORTANCE_MEDIUM => 8,
            self::IMPORTANCE_HIGH => 13,
            self::IMPORTANCE_EXTREME => 21,
        };
    }

    /**
     * Returns class to use in labeling the description counter
     *
     * @param int $count
     *
     * @return string
     */
    public function getClassForDescriptionCounter(int $count): string
    {
        if ($count > $this->maximum()) {
            $class = 'label-info';
        } elseif ($count >= $this->minimum()) {
            $class = 'label-success';
        } elseif ($count > 0) {
            $class = 'label-warning';
        } else {
            $class = 'label-danger';
        }

        return $class;
    }
}
