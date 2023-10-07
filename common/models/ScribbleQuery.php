<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[Scribble]].
 *
 * @see Scribble
 */
class ScribbleQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @return Scribble[]|array
     */
    public function all($db = null): array
    {
        return parent::all($db);
    }

    /**
     * @return Scribble|array|null
     */
    public function one($db = null): Scribble|array|null
    {
        return parent::one($db);
    }
}
