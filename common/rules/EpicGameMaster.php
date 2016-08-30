<?php

namespace common\rules;

use common\models\ParticipantRole;

final class EpicGameMaster extends EpicUser
{
    /**
     * @inheritdoc
     */
    public function requiredRole()
    {
        return ParticipantRole::ROLE_GM;
    }
}
