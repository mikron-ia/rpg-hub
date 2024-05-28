<?php

namespace frontend\assets;

use yii\web\AssetBundle;

class IndexBoxesAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [];
    public $js = [
        'js/index-boxes.js',
    ];
    public $depends = [
        'backend\assets\AppAsset',
    ];
}
