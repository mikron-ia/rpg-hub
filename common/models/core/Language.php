<?php

namespace common\models\core;

use Yii;

class Language
{
    public $language;

    static public function supportedLanguages()
    {
        if(isset(Yii::$app->params['languagesAvailable'])) {
            return Yii::$app->params['languagesAvailable'];
        } else {
            return ['en'];
        }
    }

    static public function languagesShort()
    {
        $languageData = [
            'en' => 'EN',
            'pl' => 'PL',
        ];

        $languages = [];

        foreach(self::supportedLanguages() as $language) {
            if(isset($languageData[$language])) {
                $languages[$language] = $languageData[$language];
            }
        }

        return $languages;
    }

    static public function languagesLong()
    {
        $languageData = [
            'en' => Yii::t('app', 'LANGUAGE_CODE_ENGLISH'),
            'pl' => Yii::t('app', 'LANGUAGE_CODE_POLISH'),
        ];

        $languages = [];

        foreach(self::supportedLanguages() as $language) {
            $languages[$language] = $languageData[$language];
        }

        return $languages;
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

        foreach (self::supportedLanguages() as $language) {
            $languages[] = self::create($language);
        }

        return $languages;
    }
}
