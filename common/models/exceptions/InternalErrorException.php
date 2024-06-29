<?php

namespace common\models\exceptions;

use yii\base\Exception;

class InternalErrorException extends Exception
{
    public function getName(): string
    {
        return 'Internal error exception';
    }
}
