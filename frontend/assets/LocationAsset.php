<?php

namespace frontend\assets;

use yii\web\AssetBundle;

class LocationAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [];
    public $js = [
        'js/_secrets.js',
    ];
    public $depends = [
        'backend\assets\AppAsset',
    ];
}
