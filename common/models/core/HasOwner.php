<?php

namespace common\models\core;

use common\models\User;

interface HasOwner
{
    /**
     * Verifies whether the user provided by the parameter is the user that owns the object
     *
     * @param User|\yii\web\User|null $user
     *
     * @return bool
     */
    public function isOwnedBy(User|\yii\web\User|null $user): bool;
}
