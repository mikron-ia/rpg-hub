<?php

namespace common\models\tools;

/**
 * This is the ActiveQuery class for [[IP]].
 *
 * @see IP
 */
class IpQuery extends \yii\db\ActiveQuery
{
    /**
     * @inheritdoc
     * @return IP[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return IP|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
