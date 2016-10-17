<?php

namespace common\models;

use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[StoryParameter]].
 *
 * @see StoryParameter
 */
final class StoryParameterQuery extends ActiveQuery
{
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