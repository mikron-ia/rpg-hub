<?php

namespace common\models\external;

interface ExternalComponent
{
    /**
     * Creates object from array
     * @param array|string $data
     * @return ExternalComponent
     */
    static public function createFromData($data): ExternalComponent;

    /**
     * Provides displayable content of the object
     * @return string
     */
    public function getContent(): string;
}