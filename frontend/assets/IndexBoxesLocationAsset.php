<?php

namespace frontend\assets;

use yii\web\AssetBundle;

class IndexBoxesLocationAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [];
    public $js = [
        'js/index-boxes-location.js',
    ];
    public $depends = [
        'frontend\assets\IndexBoxesAsset',
    ];
}
