<?php

namespace common\models\core;

/**
 * Interface HasVisibility is used for classes with `visibility` field in order to provide names
 * @package common\models\core
 */
interface HasVisibility
{
    /**
     * Provides list of all types of visibilities applicable to this class
     * @return array<string,string>
     */
    static public function allowedVisibilities(): array;

    /**
     * Provides the Visibility object
     */
    public function getVisibility(): Visibility;

    /**
     * Provides name of visibility category for the object as it is
     */
    public function getVisibilityName(): string;

    /**
     * Provides name of visibility category for the object in lowercase
     */
    public function getVisibilityLowercase(): string;
}
