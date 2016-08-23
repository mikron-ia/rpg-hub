<?php

namespace common\rules;

use yii\rbac\Rule;

class EpicAdministrator extends Rule
{
    /**
     * @inheritdoc
     */
    public function execute($user, $item, $params)
    {
        return false;
    }
}
