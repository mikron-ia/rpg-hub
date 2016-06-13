<?php

$environment = new \Dotenv\Dotenv(dirname(__DIR__));
$environment->load();

defined('YII_DEBUG') or define('YII_DEBUG', getenv('YII_DEBUG'));
defined('YII_ENV') or define('YII_ENV', getenv('YII_ENV'));
