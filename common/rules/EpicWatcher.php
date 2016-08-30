<?php

namespace common\rules;

use common\models\ParticipantRole;

final class EpicWatcher extends EpicUser
{
    /**
     * @inheritdoc
     */
    public function requiredRole()
    {
        return ParticipantRole::ROLE_WATCHER;
    }
}
