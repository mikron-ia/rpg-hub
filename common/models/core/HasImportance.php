<?php

namespace common\models\core;

/**
 * Interface HasImportance is used for classes with `importance` field in order to provide names
 * @package common\models\core
 */
interface HasImportance
{
    /**
     * Provides name of importance category for the object as it is
     * @return string
     */
    public function getImportance():string;

    /**
     * Provides name of importance category for the object in lowercase
     * @return string
     */
    public function getImportanceLowercase():string;
}
