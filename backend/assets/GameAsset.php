<?php

namespace backend\assets;

use yii\web\AssetBundle;

class GameAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [];
    public $js = [
        'js/game.js'
    ];
    public $depends = [
        'backend\assets\AppAsset',
    ];
}
