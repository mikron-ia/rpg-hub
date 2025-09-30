<?php
return [
    'baseUriForMail' => rtrim(getenv('URI_FRONT'), '/'),
    'indexBoxWordTrimming' => [
        'withTags' => [
            'title' => getenv('INDEX_BOX_TITLE_MAXIMUM_WORDS_WITH_TAGS'),
            'subtitle' => getenv('INDEX_BOX_SUBTITLE_MAXIMUM_WORDS_WITH_TAGS'),
        ],
        'withoutTags' => [
            'title' => getenv('INDEX_BOX_TITLE_MAXIMUM_WORDS_WITHOUT_TAGS'),
            'subtitle' => getenv('INDEX_BOX_SUBTITLE_MAXIMUM_WORDS_WITHOUT_TAGS'),
        ],
    ],
];
