<?php

namespace frontend\assets;

use yii\web\AssetBundle;

class StyleDefaultAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/style-default.css',
    ];
    public $js = [];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        AppAsset::class
    ];
}
