<?php

namespace common\rules;

use common\models\ParticipantRole;

final class EpicPlayer extends EpicUser
{
    public $name = 'epicPlayer';

    /**
     * @inheritdoc
     */
    public function requiredRole()
    {
        return ParticipantRole::ROLE_PLAYER;
    }
}
