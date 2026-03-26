<?php

namespace common\rules;

use common\models\ParticipantRole;
use Override;

final class EpicAssistant extends EpicUser
{
    public $name = 'epicAssistant';

    #[Override]
    public function requiredRole(): array
    {
        return [ParticipantRole::ROLE_ASSISTANT, ParticipantRole::ROLE_GM, ParticipantRole::ROLE_MANAGER];
    }
}
