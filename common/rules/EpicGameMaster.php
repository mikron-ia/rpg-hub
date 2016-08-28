<?php

namespace common\rules;

use common\models\ParticipantRole;

class EpicGameMaster extends EpicUser
{
    /**
     * @inheritdoc
     */
    public function requiredRole()
    {
        return ParticipantRole::ROLE_GM;
    }
}
