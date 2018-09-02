<?php

namespace frontend\assets;

use yii\web\AssetBundle;

class CharacterAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [];
    public $js = [
        'js/character.js'
    ];
    public $depends = [
        'backend\assets\AppAsset',
    ];
}