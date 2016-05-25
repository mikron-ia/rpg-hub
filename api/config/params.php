<?php
return [
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
