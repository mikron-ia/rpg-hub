<?php

namespace common\models\core;

use yii\data\ActiveDataProvider;

interface EntityQuery
{
    public function search(array $params): ActiveDataProvider;
}
