<?php

namespace backend\assets;

use yii\web\AssetBundle;

class LocationAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [];
    public $js = [
        'js/copy-key-button.js',
        'js/description-loader.js',
        'js/loader-for-location.js',
    ];
    public $depends = [
        'backend\assets\AppAsset',
    ];
}
