<?php

namespace common\rules;

use common\models\ParticipantRole;

final class EpicAssistant extends EpicUser
{
    public $name = 'epicAssistant';

    public function requiredRole()
    {
        return [ParticipantRole::ROLE_ASSISTANT, ParticipantRole::ROLE_GM, ParticipantRole::ROLE_MANAGER];
    }
}
