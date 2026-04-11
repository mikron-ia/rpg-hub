<?php

use common\models\core\ImportanceCategory;

if (getenv('LANGUAGES_ALLOWED')) {
    $languages = explode(',', str_replace(' ', '', getenv('LANGUAGES_ALLOWED')));
} else {
    $languages = ['en', 'pl'];
}

$invitationValidityMultiplier = getenv('INVITATION_VALIDITY_IN_DAYS') ?? 1;

return [
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
    'importance' => [
        'importanceWeights' => [
            'importanceCategory' => [
                ImportanceCategory::IMPORTANCE_EXTREME->value => getenv('IMPORTANCE_CATEGORY_IMPORTANCE_EXTREME_VALUE'),
                ImportanceCategory::IMPORTANCE_HIGH->value => getenv('IMPORTANCE_CATEGORY_IMPORTANCE_HIGH_VALUE'),
                ImportanceCategory::IMPORTANCE_MEDIUM->value => getenv('IMPORTANCE_CATEGORY_IMPORTANCE_MEDIUM_VALUE'),
                ImportanceCategory::IMPORTANCE_LOW->value => getenv('IMPORTANCE_CATEGORY_IMPORTANCE_LOW_VALUE'),
                ImportanceCategory::IMPORTANCE_NONE->value => getenv('IMPORTANCE_CATEGORY_IMPORTANCE_NONE_VALUE'),
            ],
            'newAndUpdated' => [
                'new' => getenv('IMPORTANCE_NEW_VALUE'),
                'updated' => getenv('IMPORTANCE_UPDATED_VALUE'),
                'default' => getenv('IMPORTANCE_DEFAULT_VALUE'),
            ],
            'associated' => [
                'associated' => getenv('IMPORTANCE_ASSOCIATED_VALUE'),
                'unassociated' => getenv('IMPORTANCE_UNASSOCIATED_VALUE'),
            ],
            'date' => [
                'initial' => getenv('IMPORTANCE_DATE_INITIAL_VALUE'),
                'divider' => getenv('IMPORTANCE_DATE_DIVIDER_VALUE'),
            ],
        ],
    ],
    'invitation.isValidFor' => $invitationValidityMultiplier * 86400,
    'keyGeneration' => [
        'announcement' => getenv('KEY_GENERATION_ANNOUNCEMENT'),
        'article' => getenv('KEY_GENERATION_ARTICLE'),
        'character' => getenv('KEY_GENERATION_CHARACTER'),
        'characterSheet' => getenv('KEY_GENERATION_CHARACTER_SHEET'),
        'description' => getenv('KEY_GENERATION_DESCRIPTION'),
        'descriptionPack' => getenv('KEY_GENERATION_DESCRIPTION_PACK'),
        'epic' => getenv('KEY_GENERATION_EPIC'),
        'externalData' => getenv('KEY_GENERATION_EXTERNAL_DATA'),
        'externalDataPack' => getenv('KEY_GENERATION_EXTERNAL_DATA_PACK'),
        'game' => getenv('KEY_GENERATION_GAME'),
        'group' => getenv('KEY_GENERATION_GROUP'),
        'groupMembership' => getenv('KEY_GENERATION_GROUP_MEMBERSHIP'),
        'parameter' => getenv('KEY_GENERATION_PARAMETER'),
        'parameterPack' => getenv('KEY_GENERATION_PARAMETER_PACK'),
        'participant' => getenv('KEY_GENERATION_PARTICIPANT'),
        'person' => getenv('KEY_GENERATION_CHARACTER'),
        'pointInTime' => getenv('KEY_GENERATION_POINT_IN_TIME'),
        'recap' => getenv('KEY_GENERATION_RECAP'),
        'scenario' => getenv('KEY_GENERATION_SCENARIO'),
        'scribble' => getenv('KEY_GENERATION_SCRIBBLE'),
        'story' => getenv('KEY_GENERATION_STORY'),
        'storyCharacterAssignment' => getenv('KEY_GENERATION_STORY_CHARACTER_ASSIGNMENT'),
        'storyGroupAssignment' => getenv('KEY_GENERATION_STORY_GROUP_ASSIGNMENT'),
        'user' => getenv('KEY_GENERATION_USER'),
        'userInvitation' => getenv('KEY_GENERATION_USER_INVITATION'),
    ],
    'languagesAvailable' => $languages, // Languages will appear in the order entered here
    'reputationAccessUri' => getenv('REPUTATION_URI'),
    'reputationAccessKey' => getenv('AUTHENTICATION_REPUTATION_SIMPLE_KEY'),
    'senderEmail' => getenv('EMAIL'),
    'uri.front' => rtrim(getenv('URI_FRONT'), '/'),
    'uri.back' => rtrim(getenv('URI_BACK'), '/'),
];