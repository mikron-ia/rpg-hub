<?php

if(getenv('LANGUAGES_ALLOWED')) {
    $languages = explode(',',str_replace(' ','',getenv('LANGUAGES_ALLOWED')));
} else {
    $languages = ['en', 'pl'];
}

return [
    'adminEmail' => 'admin@example.com',
    'supportEmail' => 'support@example.com',
    'user.passwordResetTokenExpire' => 3600,
    'authenticationReferences' => [
        'authenticationMethodReference' => [
            'auth-simple' => 'simple',
        ],
    ],
    'authentication' => [
        'front' => [
            'allowedStrategies' => ['simple'],
            'settingsByStrategy' => [
                'simple' => [
                    'authenticationKey' => getenv('AUTHENTICATION_SIMPLE_KEY')
                ]
            ]
        ],
        'reputation' => [
            'allowedStrategies' => [],
            'settingsByStrategy' => []
        ],
    ],
    'keyGeneration' => [
        'character' => getenv('KEY_GENERATION_CHARACTER'),
        'epic' => getenv('KEY_GENERATION_EPIC'),
        'group' => getenv('KEY_GENERATION_GROUP'),
        'person' => getenv('KEY_GENERATION_PERSON'),
        'recap' => getenv('KEY_GENERATION_RECAP'),
        'story' => getenv('KEY_GENERATION_STORY'),
    ],
    'languagesAvailable' => $languages, // Languages will appear in the order entered here
    'reputationAccessUri' => getenv('REPUTATION_URI'),
    'reputationAccessKey' => getenv('AUTHENTICATION_REPUTATION_SIMPLE_KEY'),
];