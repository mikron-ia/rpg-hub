<?php

namespace common\models\core;

interface HasKey
{
    public static function keyParameterName(): string;
}
