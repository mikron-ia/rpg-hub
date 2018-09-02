<?php

namespace frontend\assets;

use yii\web\AssetBundle;

class StoryAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [];
    public $js = [
        'js/story.js',
    ];
    public $depends = [
        'backend\assets\AppAsset',
    ];
}
