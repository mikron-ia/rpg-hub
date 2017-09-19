<?php

namespace common\models\core;

/**
 * Interface IsPack
 * @package common\models\core
 */
interface IsPack
{
    /**
     * @return bool
     */
    public function canUserReadYou(): bool;

    /**
     * @return bool
     */
    public function canUserControlYou(): bool;
}
