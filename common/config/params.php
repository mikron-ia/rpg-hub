<?php
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
                    'authenticationKey' => '[enter key]'
                ]
            ]
        ],
        'reputation' => [
            'allowedStrategies' => [],
            'settingsByStrategy' => []
        ],
    ],
    'keyGeneration' => [
        'character' => 'character-key-base-{number0}-{number1}-{number2}-{number3}-{number4}',
        'epic' => 'epic-key-base-{number0}-{number1}-{number2}-{number3}-{number4}',
        'group' => 'group-key-base-{number0}-{number1}-{number2}-{number3}-{number4}',
        'person' => 'person-key-base-{number0}-{number1}-{number2}-{number3}-{number4}',
        'recap' => 'recap-key-base-{number0}-{number1}-{number2}-{number3}-{number4}',
        'story' => 'story-key-base-{number0}-{number1}-{number2}-{number3}-{number4}',
    ],
];
