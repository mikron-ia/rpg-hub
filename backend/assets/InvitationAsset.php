<?php

namespace backend\assets;

use yii\web\AssetBundle;

class InvitationAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [];
    public $js = [
        'js/invitation.js',
    ];
    public $depends = [
        'backend\assets\AppAsset',
    ];
}
