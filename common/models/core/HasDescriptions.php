<?php

namespace common\models\core;

/**
 * Interface HasSDescriptions describes classes with attached DescriptionPack that want to use full functionality of the pack
 * @package common\models\core
 */
interface HasDescriptions
{
    /**
     * Provides list of types allowed by this class
     * Types should be listed in order desired in select
     * @return string[]
     */
    static public function allowedDescriptionTypes(): array;

    /**
     * Provides ID of the description pack
     * @return int
     */
    public function getDescriptionPackId(): int;
}
