<?php

namespace common\models\core;

enum ImageDisplayMode: string
{
    case Always = 'always';
    case Backup = 'backup';
    case Never = 'never';
}
