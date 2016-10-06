<?php

namespace common\rules;

use common\models\Participant;
use Yii;
use yii\rbac\Rule;
use yii\web\HttpException;

final class EpicParticipant extends Rule
{
    public $name = 'epicParticipant';

    /**
     * @inheritdoc
     */
    public function execute($user, $item, $params)
    {
        if(!$params['epic']) {
            throw new HttpException(403, Yii::t('app', 'ERROR_UNABLE_TO_CHECK_RIGHTS_MISSING_EPIC'));
        }

        /* @var $participant Participant */
        $participant = Participant::findOne([
            'epic_id' => $params['epic']->epic_id,
            'user_id' => $user
        ]);

        return ($participant !== null);
    }
}
