<?php

namespace backend\assets;

use yii\web\AssetBundle;

class ImageAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [];
    public $js = [
        'js/copy-key-button.js',
        'js/image-link-handler.js',
    ];
    public $depends = [
        'backend\assets\AppAsset',
    ];
}
