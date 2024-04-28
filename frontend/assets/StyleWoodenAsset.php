<?php

namespace frontend\assets;

use yii\web\AssetBundle;

class StyleWoodenAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/style-wooden.css',
    ];
    public $js = [
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        AppAsset::class
    ];
}
