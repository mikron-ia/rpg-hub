<?php
return [
    'baseUriForMail' => rtrim(getenv('URI_FRONT'), '/'),
    'indexBoxWordTrimming' => [
        'title' => getenv('INDEX_BOX_TITLE_MAXIMUM_WORDS'),
        'subtitle' => getenv('INDEX_BOX_SUBTITLE_MAXIMUM_WORDS'),
    ],
];
