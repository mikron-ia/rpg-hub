<?php

namespace common\rules;

use common\models\ParticipantRole;
use Override;

final class EpicGameMaster extends EpicUser
{
    public $name = 'epicGameMaster';

    #[Override]
    public function requiredRole(): array
    {
        return [ParticipantRole::ROLE_GM, ParticipantRole::ROLE_MANAGER];
    }
}
