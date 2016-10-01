<?php

namespace common\rules;

use common\models\ParticipantRole;

final class EpicWatcher extends EpicUser
{
    public $name = 'epicWatcher';

    /**
     * @inheritdoc
     */
    public function requiredRole()
    {
        return ParticipantRole::ROLE_WATCHER;
    }
}
