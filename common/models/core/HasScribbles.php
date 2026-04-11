<?php

namespace common\models\core;

use common\models\Scribble;

interface HasScribbles
{
    public function getScribbleForCurrentUser(): ?Scribble;
}
