<?php

namespace common\models\external;

interface ExternalComponent
{
    /**
     * Creates the object from an array
     */
    public static function createFromData(array|string $data): ExternalComponent;

    /**
     * Provides displayable content of the object
     * @return string
     */
    public function getContent(): string;
}