<?php

namespace common\rules;

use yii\rbac\Rule;

class EpicParticipant extends Rule
{
    /**
     * @inheritdoc
     */
    public function execute($user, $item, $params)
    {
        return false;
    }
}
