<?php
/**
 * Configuration file for 'yii message/extract' command
 */
return [
    'sourcePath' => __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR,
    'languages' => ['en', 'pl'],
    'translator' => 'Yii::t',
    'sort' => true,
    'removeUnused' => true,
    'only' => ['*.php'],
    'except' => [
        '.git',
        '.gitignore',
        '.gitkeep',
        'messages',
        'vendor',
    ],
    'format' => 'php',
    'messagePath' => __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'messages',
    'overwrite' => true,
];
