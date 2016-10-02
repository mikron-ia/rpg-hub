<?php

namespace common\rules;

use common\models\ParticipantRole;

final class EpicAssistant extends EpicUser
{
    public $name = 'epicAssistant';

    /**
     * @inheritdoc
     */
    public function requiredRole()
    {
        return ParticipantRole::ROLE_ASSISTANT;
    }
}
