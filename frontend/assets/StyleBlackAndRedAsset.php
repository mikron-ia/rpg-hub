<?php

namespace frontend\assets;

use yii\web\AssetBundle;

class StyleBlackAndRedAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/style-black-and-red.css',
    ];
    public $js = [
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        AppAsset::class
    ];
}
