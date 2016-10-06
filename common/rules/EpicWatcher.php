<?php

namespace common\rules;

use common\models\ParticipantRole;

final class EpicWatcher extends EpicUser
{
    public $name = 'epicWatcher';

    public function requiredRole()
    {
        return [ParticipantRole::ROLE_WATCHER, ParticipantRole::ROLE_ASSISTANT, ParticipantRole::ROLE_GM, ParticipantRole::ROLE_PLAYER];
    }
}
