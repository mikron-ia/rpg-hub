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
];
