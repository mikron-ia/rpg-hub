<?php

require('./vendor/autoload.php');
require(__DIR__ . '/../common/environment.php');
require('./vendor/yiisoft/yii2/Yii.php');

Yii::setAlias('@common', dirname(__DIR__));
Yii::setAlias('@frontend', dirname(dirname(__DIR__)) . '/frontend');
Yii::setAlias('@backend', dirname(dirname(__DIR__)) . '/backend');
Yii::setAlias('@console', dirname(dirname(__DIR__)) . '/console');

$config = require(__DIR__ .'/../common/config/main.php');

$config['id'] = 'app-tests';
$config['basePath'] = dirname(__DIR__);

(new yii\web\Application($config));