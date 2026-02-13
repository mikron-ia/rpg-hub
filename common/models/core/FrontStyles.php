<?php

namespace common\models\core;

use frontend\assets\StyleBlackAndRedAsset;
use frontend\assets\StyleDefaultAsset;
use frontend\assets\StyleWoodenAsset;
use Yii;

enum FrontStyles: string
{
    case Default = 'default';
    case Wooden = 'wooden';
    case BlackAndRed = 'black-and-red';

    public function provideClass(): string
    {
        return match ($this) {
            self::Default => StyleDefaultAsset::class,
            self::Wooden => StyleWoodenAsset::class,
            self::BlackAndRed => StyleBlackAndRedAsset::class,
        };
    }

    public function getStyleName(): string
    {
        return self::provideStyleNames()[$this->value];
    }

    /**
     * @return string[]
     */
    public static function provideStyleNames(): array
    {
        return [
            self::Default->value => Yii::t('app', 'FRONT_STYLE_NAME_DEFAULT'),
            self::Wooden->value => Yii::t('app', 'FRONT_STYLE_NAME_WOODEN'),
            self::BlackAndRed->value => Yii::t('app', 'FRONT_STYLE_NAME_BLACK_AND_RED'),
        ];
    }
}
