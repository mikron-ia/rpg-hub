<?php

namespace common\models;

use Override;
use yii\db\ActiveQuery;

/**
 * @see Scribble
 */
class ScribbleQuery extends ActiveQuery
{
    /**
     * @return Scribble[]
     */
    #[Override]
    public function all($db = null): array
    {
        return parent::all($db);
    }

    #[Override]
    public function one($db = null): Scribble|array|null
    {
        return parent::one($db);
    }
}
