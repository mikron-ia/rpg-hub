<?php

namespace common\models\core;

enum UserStatus: int
{
    case Forgotten = 0; // deleted and forgotten, data erased from current database
    case Deleted = 1; // detached from everything and deleted, archival data retained
    case Disabled = 5; // deactivated, cannot log in, can be reactivated
    case Active = 10; // fully active, fully visible
}
