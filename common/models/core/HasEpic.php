<?php

namespace common\models\core;

use yii\db\ActiveQuery;

interface HasEpic
{
    public function getEpic(): ActiveQuery;
}
