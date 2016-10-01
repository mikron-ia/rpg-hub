<?php

namespace common\rules;

use common\models\ParticipantRole;

final class EpicManager extends EpicUser
{
    public $name = 'epicManager';

    /**
     * @inheritdoc
     */
    public function requiredRole()
    {
        return ParticipantRole::ROLE_MANAGER;
    }
}
