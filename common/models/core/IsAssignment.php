<?php

namespace common\models\core;

interface IsAssignment
{
    public static function create(int $actingSideId, int $narrativeSideId, Visibility $visibility): self;
}
