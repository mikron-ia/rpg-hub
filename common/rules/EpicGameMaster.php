<?php

namespace common\rules;

use common\models\ParticipantRole;

final class EpicGameMaster extends EpicUser
{
    public $name = 'epicGameMaster';

    /**
     * @inheritdoc
     */
    public function requiredRole()
    {
        return ParticipantRole::ROLE_GM;
    }
}
