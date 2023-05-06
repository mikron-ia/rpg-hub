<?php

use yii\i18n\PhpMessageSource;

$mailer = [
    'class' => 'yii\swiftmailer\Mailer',
    'viewPath' => '@common/mail',
];

if (getenv('MAIL_SMTP')) {
    $mailer['useFileTransport'] = false;
    $mailer['transport'] = [
        'class' => 'Swift_SmtpTransport',
        'host' => getenv('MAIL_HOST'),
        'username' => getenv('MAIL_USER'),
        'password' => getenv('MAIL_PASS'),
        'port' => getenv('MAIL_PORT'),
        'encryption' => getenv('MAIL_ENCRYPTION'),
    ];
} else {
    $mailer['useFileTransport'] = true;
}

return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'language' => 'en',
    'components' => [
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
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
        'formatter' => [
            'dateFormat' => 'yyyy-MM-dd',
            'datetimeFormat' => 'yyyy-MM-dd HH:mm:ss',
        ],
        'i18n' => [
            'translations' => [
                'app*' => [
                    'class' => PhpMessageSource::class,
                    'basePath' => '@common/messages',
                ],
                'external*' => [
                    'class' => PhpMessageSource::class,
                    'basePath' => '@common/messages',
                ],
                'mail*' => [
                    'class' => PhpMessageSource::class,
                    'basePath' => '@common/messages',
                ],
            ],
        ],
        'mailer' => $mailer,
    ],
    'modules' => [],
];
