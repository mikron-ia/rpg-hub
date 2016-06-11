<?php

namespace common\models\core;

use Yii;

class Language
{
    static public $supportedLanguages = ['en', 'pl'];

    public $language;

    static public function languagesShort()
    {
        return [
            'en' => 'EN',
            'pl' => 'PL',
        ];
    }

    static public function languagesLong()
    {
        return [
            'en' => Yii::t('app', 'LANGUAGE_CODE_ENGLISH'),
            'pl' => Yii::t('app', 'LANGUAGE_CODE_POLISH'),
        ];
    }

    static public function create($code)
    {
        return new Language(['language' => $code]);
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        $tradeNames = self::languagesLong();
        return isset($tradeNames[$this->language]) ? $tradeNames[$this->language] : null;
    }
}
