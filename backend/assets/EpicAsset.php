<?php

namespace backend\assets;

use yii\web\AssetBundle;

class EpicAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [];
    public $js = [
        'js/epic.js',
    ];
    public $depends = [
        'backend\assets\AppAsset',
    ];
}
