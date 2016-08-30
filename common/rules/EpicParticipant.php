<?php

namespace common\rules;

use common\models\Participant;
use yii\rbac\Rule;

final class EpicParticipant extends Rule
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

        return ($participant !== null);
    }
}
