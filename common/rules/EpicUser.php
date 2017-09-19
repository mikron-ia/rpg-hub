<?php

namespace common\rules;

use common\models\Participant;
use common\models\ParticipantRole;
use Yii;
use yii\rbac\Rule;
use yii\web\HttpException;

abstract class EpicUser extends Rule
{
    public function execute($user, $item, $params)
    {
        if (!isset($params['epic'])) {
            throw new HttpException(403, Yii::t('app', 'ERROR_UNABLE_TO_CHECK_RIGHTS_MISSING_EPIC'));
        }

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

    /**
     * Provides code for the role to check against
     * @return string[]
     */
    abstract public function requiredRole();
}
