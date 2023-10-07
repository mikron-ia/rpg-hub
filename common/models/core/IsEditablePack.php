<?php

namespace common\models\core;

/**
 * Interface IsEditablePack
 * @package common\models\core
 */
interface IsEditablePack extends IsPack
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
