<?php

namespace common\models\core;

/**
 * Interface for packs that self-fill if empty
 */
interface IsSelfFillingPack extends IsPack
{
    public function createEmptyContent(int $userId);
}
