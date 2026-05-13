<?php

namespace common\models\core;

use common\models\type\AssignmentRank;

interface IsAssignment
{
    public static function create(int $actingSideId, int $narrativeSideId, Visibility $visibility, AssignmentRank $rank): self;

    public function getActingSideId(): int;

    public function getNarrativeSideId(): int;

    public function getRank(): AssignmentRank;
}
