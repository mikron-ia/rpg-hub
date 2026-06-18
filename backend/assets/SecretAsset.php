<?php

namespace backend\assets;

use yii\web\AssetBundle;

class SecretAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [];
    public $js = [
        'js/bestower.js',
        'js/copy-key-button.js',
        'js/secret-bestower.js',
    ];
    public $depends = [
        'backend\assets\AppAsset',
    ];
}
