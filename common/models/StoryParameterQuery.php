<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[StoryParameter]].
 *
 * @see StoryParameter
 */
class StoryParameterQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return StoryParameter[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return StoryParameter|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}