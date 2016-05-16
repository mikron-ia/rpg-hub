<?php

namespace common\models;

interface Displayable
{
    /**
     * @return array Simple representation of the object content, fit for basic display
     */
    public function getSimpleData();

    /**
     * @return array Complete representation of public parts of object content, fit for full card display
     */
    public function getCompleteData();
}
