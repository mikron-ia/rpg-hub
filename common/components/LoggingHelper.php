<?php

namespace common\components;

class LoggingHelper
{
    /**
     * @param string $message
     * @param string $prefix
     */
    public static function log(string $message, string $prefix = '')
    {
        $dateTime = date("Y-m-d H:i:s");
        echo "[$dateTime]" . (empty($prefix) ? '' : "[$prefix]") . ' ' . $message . PHP_EOL;
    }
}
