<?php

namespace common\models;

use yii\db\ActiveRecord;

/**
 * This is the ActiveQuery class for [[ScribblePack]].
 *
 * @see ScribblePack
 */
class ScribblePackQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @return ScribblePack[]|array
     */
    public function all($db = null): array
    {
        return parent::all($db);
    }

    /**
     * @param null $db
     * @return array|ActiveRecord|null
     */
    public function one($db = null): array|ActiveRecord|null
    {
        return parent::one($db);
    }
}
