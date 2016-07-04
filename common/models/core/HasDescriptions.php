<?php

namespace common\models\core;

/**
 * Interface HasSDescriptions describes classes with attached DescriptionPack that want to use full functionality of the pack
 *
 * @package common\models\core
 */
interface HasDescriptions
{
    /**
     * Provides list of types allowed by this class
     * @return string[]
     */
    static public function allowedDescriptionTypes();
}
