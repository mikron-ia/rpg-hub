<?php

namespace common\rules;

use common\models\ParticipantRole;

class EpicWatcher extends EpicUser
{
    /**
     * @inheritdoc
     */
    public function requiredRole()
    {
        return ParticipantRole::ROLE_WATCHER;
    }
}
