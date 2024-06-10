<?php

namespace backend\assets;

use yii\web\AssetBundle;

class GroupAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [];
    public $js = [
        'js/copy-key-button.js',
        'js/description-user.js',
        'js/group.js',
        'js/group-memberships.js',
        'js/tooltip.js',
    ];
    public $depends = [
        'backend\assets\AppAsset',
    ];
}
