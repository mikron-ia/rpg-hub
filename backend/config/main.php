<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

if (getenv('LANGUAGES_ALLOWED')) {
    $languages = explode(',', str_replace(' ', '', getenv('LANGUAGES_ALLOWED')));
} else {
    $languages = ['en', 'pl'];
}

$config = [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => [
        'log',
        [
            'class' => common\components\LanguageSelector::class,
            'supportedLanguages' => $languages,
        ],
        [
            'class' => common\components\EpicSelector::class,
        ]
    ],
    'modules' => [],
    'components' => [
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => yii\log\FileTarget::class,
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            //'showScriptName' => false,
            //'rules' => [],
        ],
    ],
    'name' => 'RPG Hub - control panel',
    'params' => $params,
];

return $config;