<?php

namespace common\models\core;

/**
 * Interface Displayable denotes a class that can be sent via API as an independent whole
 *
 * @package common\models\core
 */
interface Displayable
{
    /**
     * Provides a simple representation of the object content, fit for basic display in an index or a summary
     *
     * @return array<string,string|array>
     */
    public function getSimpleDataForApi(): array;

    /**
     * Provides complete representation of public parts of object content, fit for full card display
     *
     * @return array<string,string|array>
     */
    public function getCompleteDataForApi(): array;

    /**
     * Answers whether the object should be visible via API
     */
    public function isVisibleInApi(): bool;
}
