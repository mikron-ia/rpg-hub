<?php

$environment = \Dotenv\Dotenv::createUnsafeImmutable(dirname(__DIR__)); // either use unsafe or remove all getenv()
$environment->load();

defined('YII_DEBUG') or define('YII_DEBUG', getenv('YII_DEBUG'));
defined('YII_ENV') or define('YII_ENV', getenv('YII_ENV'));
