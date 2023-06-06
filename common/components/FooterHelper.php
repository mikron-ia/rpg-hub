<?php


namespace common\components;

use Yii;
use yii\helpers\Html;

class FooterHelper
{
    public static function copyright(): string
    {
        return '&copy; Mikron ' . date('Y');
    }

    public static function powered(): string
    {
        return Html::a(
            Yii::t('app', 'FOOTER_ABOUT_LINK'),
            ['site/about']
        );
    }
}
