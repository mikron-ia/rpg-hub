<?php

namespace common\models\tools;

use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[IP]].
 *
 * @see IP
 */
class IpQuery extends ActiveQuery
{
    public function all($db = null)
    {
        return parent::all($db);
    }

    public function one($db = null)
    {
        return parent::one($db);
    }
}
