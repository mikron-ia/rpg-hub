<?php
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'language' => 'en',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => getenv('DB_DSN'),
            'username' => getenv('DB_USERNAME'),
            'password' => getenv('DB_PASSWORD'),
            'charset' => getenv('DB_CHARSET'),
        ],
        'i18n' => [
            'translations' => [
                'app*' => [
                    'class' => \yii\i18n\PhpMessageSource::className(),
                    'basePath' => '@common/messages',
                ],
            ],
        ],
    ],
    'modules' => [],
];
