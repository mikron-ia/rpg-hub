<?php

namespace common\models\external;


interface ExternalComponent
{
    /**
     * Creates object from array
     * @param array $array
     * @return ExternalComponent
     */
    static public function createFromArray(array $array): ExternalComponent;

    public function __toString();
}