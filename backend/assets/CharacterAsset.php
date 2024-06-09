<?php

namespace backend\assets;

use yii\web\AssetBundle;

class CharacterAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [];
    public $js = [
        'js/character.js',
        'js/copy-key-button.js',
        'js/description-user.js',
        'js/tooltip.js',
    ];
    public $depends = [
        'backend\assets\AppAsset',
    ];
}
