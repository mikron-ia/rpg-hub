<?php

namespace backend\assets;

use yii\web\AssetBundle;

class StoryAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [];
    public $js = [
        'js/copy-key-button.js',
        'js/parameter-handler.js',
        'js/story.js',
        'js/story-assignments.js',
    ];
    public $depends = [
        'backend\assets\AppAsset',
    ];
}
