<?php

namespace common\models\core;

use Yii;

/**
 * Class ImportanceCategory
 * @package common\models\core
 */
final class ImportanceCategory
{
    const IMPORTANCE_NONE = '4-none';
    const IMPORTANCE_LOW = '3-low';
    const IMPORTANCE_MEDIUM = '2-medium';
    const IMPORTANCE_HIGH = '1-high';
    const IMPORTANCE_EXTREME = '0-extreme';

    /**
     * @var string
     */
    public $importance;

    /**
     * @param $code
     * @return ImportanceCategory
     */
    static public function create($code): ImportanceCategory
    {
        $importance = new ImportanceCategory();
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
     * @return string[]
     */
    static private function importanceNamesSimple(): array
    {
        return [
            self::IMPORTANCE_NONE => Yii::t('app', 'IMPORTANCE_NONE'),
            self::IMPORTANCE_LOW => Yii::t('app', 'IMPORTANCE_LOW'),
            self::IMPORTANCE_MEDIUM => Yii::t('app', 'IMPORTANCE_MEDIUM'),
            self::IMPORTANCE_HIGH => Yii::t('app', 'IMPORTANCE_HIGH'),
            self::IMPORTANCE_EXTREME => Yii::t('app', 'IMPORTANCE_EXTREME'),
        ];
    }

    /**
     * @return string[]
     */
    static private function importanceNamesWithDescriptions(): array
    {
        $descriptions = self::importanceNamesDescriptions();

        $namesWithDescriptions = self::importanceNamesSimple();

        array_walk($namesWithDescriptions, function (string &$name, string $key) use ($descriptions) {
            $name .= ' (' . $descriptions[$key] . ')';
        });

        return $namesWithDescriptions;
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
        $names = $includeDescriptions ? self::importanceNamesWithDescriptions() : self::importanceNamesSimple();

        $allowed = self::allowedImportance();

        foreach ($names as $key => $name) {
            if (!in_array($key, $allowed)) {
                unset($names[$key]);
            }
        }

        return $names;
    }

    /**
     * Lists allowed importance
     * @return string[]
     */
    static public function allowedImportance(): array
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
     * Provides importance name in lowercase
     * @return string
     */
    public function getNameLowercase(): string
    {
        $names = self::importanceNamesLowercase();
        return isset($names[$this->importance]) ? $names[$this->importance] : '?';
    }

    /**
     * Provides importance names in lowercase
     *
     * @return string[]
     */
    static public function importanceNamesLowercase(): array
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
     * Provides importance names' descriptions
     *
     * @return string[]
     */
    static public function importanceNamesDescriptions(): array
    {
        return [
            self::IMPORTANCE_NONE => Yii::t('app', 'IMPORTANCE_NONE_DESCRIPTION'),
            self::IMPORTANCE_LOW => Yii::t('app', 'IMPORTANCE_LOW_DESCRIPTION'),
            self::IMPORTANCE_MEDIUM => Yii::t('app', 'IMPORTANCE_MEDIUM_DESCRIPTION'),
            self::IMPORTANCE_HIGH => Yii::t('app', 'IMPORTANCE_HIGH_DESCRIPTION'),
            self::IMPORTANCE_EXTREME => Yii::t('app', 'IMPORTANCE_EXTREME_DESCRIPTION'),
        ];
    }
}
