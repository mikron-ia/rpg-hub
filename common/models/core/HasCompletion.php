<?php

namespace common\models\core;


interface HasCompletion
{
    /**
     * Provides completion percentage as integer
     * @return int|null
     */
    public function getCompletionPercentage(): ?int;
}
