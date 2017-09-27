<?php

namespace common\models\external;

use yii\base\Model;

class Tab extends Model
{
    /**
     * @var string
     */
    public $title;

    /**
     * @var Box[]
     */
    public $boxes;
}
