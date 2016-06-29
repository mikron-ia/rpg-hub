<?php

namespace common\models\core;

/**
 * Interface HasParameters describes classes with attached ParameterPack that want to use full functionality of the pack
 *
 * @package common\models\core
 */
interface HasParameters
{
    /**
     * Provides list of types allowed by this class
     * @return string[]
     */
    public function allowedTypes();
}
