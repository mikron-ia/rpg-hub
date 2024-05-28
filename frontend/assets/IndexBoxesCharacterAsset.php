<?php

namespace frontend\assets;

use yii\web\AssetBundle;

class IndexBoxesCharacterAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [];
    public $js = [
        'js/index-boxes-character.js',
    ];
    public $depends = [
        'frontend\assets\IndexBoxesAsset',
    ];
}
