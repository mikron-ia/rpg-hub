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
        $language = new Language();
        $language->language = $code;
        return $language;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        $names = self::languagesLong();
        return isset($names[$this->language]) ? $names[$this->language] : null;
    }

    /**
     * @return Language[]
     */
    static public function getLanguagesAsObjects()
    {
        $languages = [];

        foreach (self::$supportedLanguages as $language) {
            $languages[] = self::create($language);
        }

        return $languages;
    }
}
