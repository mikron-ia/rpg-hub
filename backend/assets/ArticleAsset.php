<?php

namespace backend\assets;

use yii\web\AssetBundle;

class ArticleAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [];
    public $js = [
        'js/copy-key-button.js',
    ];
    public $depends = [
        'backend\assets\AppAsset',
    ];
}
