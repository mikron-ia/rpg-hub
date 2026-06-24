<?php

namespace frontend\assets;

use yii\web\AssetBundle;

class ProjectAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [];
    public $js = [
        'js/_secrets.js',
        'js/project.js',
    ];
    public $depends = [
        'backend\assets\AppAsset',
    ];
}
