<?php

namespace common\rules;

use common\models\ParticipantRole;

final class EpicManager extends EpicUser
{
    /**
     * @inheritdoc
     */
    public function requiredRole()
    {
        return ParticipantRole::ROLE_MANAGER;
    }
}
