<?php

namespace common\rules;

use common\models\ParticipantRole;
use Override;

final class EpicWatcher extends EpicUser
{
    public $name = 'epicWatcher';

    #[Override]
    public function requiredRole(): array
    {
        return [
            ParticipantRole::ROLE_WATCHER,
            ParticipantRole::ROLE_ASSISTANT,
            ParticipantRole::ROLE_GM,
            ParticipantRole::ROLE_PLAYER,
            ParticipantRole::ROLE_MANAGER,
        ];
    }
}
