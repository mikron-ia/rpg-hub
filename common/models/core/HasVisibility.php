<?php

namespace common\models\core;

/**
 * Interface HasVisibility is used for classes with `visibility` field in order to provide names
 * @package common\models\core
 */
interface HasVisibility
{
    /**
     * Provides name of visibility category for the object as it is
     * @return string
     */
    public function getVisibility():string;

    /**
     * Provides name of visibility category for the object in lowercase
     * @return string
     */
    public function getVisibilityLowercase():string;
}
