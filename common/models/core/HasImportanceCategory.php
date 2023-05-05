<?php

namespace common\models\core;

/**
 * Interface HasImportanceCategory is used for classes with `importance_category` field in order to provide names
 * @package common\models\core
 */
interface HasImportanceCategory
{
    /**
     * Provides name of importance category for the object as it is
     * @return string
     */
    public function getImportanceCategory(): string;

    /**
     * Provides name of importance category for the object in lowercase
     * @return string
     */
    public function getImportanceCategoryLowercase(): string;

    /**
     * @return ImportanceCategory
     */
    public function getImportanceCategoryObject(): ImportanceCategory;
}
