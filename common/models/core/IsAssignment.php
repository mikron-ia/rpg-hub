<?php

namespace common\models\core;

use common\models\exceptions\InternalErrorException;

interface IsAssignment
{
    public static function create(int $actingSideId, int $narrativeSideId, Visibility $visibility): self;

    public function getActingSideId(): int;

    public function getNarrativeSideId(): int;

//    public function getType(): AssignmentType;
}
