<?php

namespace common\models\core;

enum UserStatus: int
{
    case Deleted = 0;
    case Active = 10;
}
