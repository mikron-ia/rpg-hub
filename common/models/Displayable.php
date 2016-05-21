<?php

namespace common\models;

/**
 * Interface Displayable denotes a class that can be sent via API as an independent whole
 * @package common\models
 */
interface Displayable
{
    /**
     * Provides simple representation of the object content, fit for basic display in an index or a summary
     * @return array
     */
    public function getSimpleData();

    /**
     * Provides complete representation of public parts of object content, fit for full card display
     * @return array
     */
    public function getCompleteData();
}
