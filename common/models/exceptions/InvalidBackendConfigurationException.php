<?php

namespace common\models\exceptions;

class InvalidBackendConfigurationException extends InternalErrorException
{
    public function getName(): string
    {
        return 'Invalid backend configuration exception';
    }
}
