<?php

namespace frontend\assets;

use yii\web\AssetBundle;

class GroupAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [];
    public $js = [
        'js/group.js',
        'js/description.js',
        'js/_secrets.js',
    ];
    public $depends = [
        'backend\assets\AppAsset',
    ];
}
