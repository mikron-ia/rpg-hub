<?php

namespace common\rules;

use common\models\ParticipantRole;
use Override;

final class EpicPlayer extends EpicUser
{
    public $name = 'epicPlayer';

    #[Override]
    public function requiredRole(): array
    {
        return [ParticipantRole::ROLE_PLAYER, ParticipantRole::ROLE_GM, ParticipantRole::ROLE_MANAGER];
    }
}
