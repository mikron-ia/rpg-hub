<?php

namespace frontend\assets;

use yii\web\AssetBundle;

class IndexBoxesGroupAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [];
    public $js = [
        'js/index-boxes-group.js',
    ];
    public $depends = [
        'frontend\assets\IndexBoxesAsset',
    ];
}
