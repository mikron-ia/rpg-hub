<?php

namespace backend\assets;

use yii\web\AssetBundle;

class ScenarioAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [];
    public $js = [
        'js/description-user.js',
        'js/scenario.js',
        'js/tooltip.js',
    ];
    public $depends = [
        'backend\assets\AppAsset',
    ];
}
