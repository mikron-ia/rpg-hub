<?php

namespace common\rules;

use common\models\Participant;
use common\models\ParticipantRole;
use yii\rbac\Rule;

abstract class EpicUser extends Rule
{
    /**
     * @inheritdoc
     */
    public function execute($user, $item, $params)
    {
        /* @var $participant Participant */
        $participant = Participant::findOne([
            'epic_id' => $params['epic']->epic_id,
            'user_id' => $user
        ]);

        if (!$participant) {
            return false;
        }

        $role = ParticipantRole::findOne([
            'participant_id' => $participant->participant_id,
            'role' => $this->requiredRole()
        ]);

        return ($role !== null);
    }

    abstract public function requiredRole();
}
