<?php

namespace common\rules;

use common\models\ParticipantRole;

class EpicPlayer extends EpicUser
{
    /**
     * @inheritdoc
     */
    public function requiredRole()
    {
        return ParticipantRole::ROLE_PLAYER;
    }
}
