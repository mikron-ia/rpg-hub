<?php

namespace common\models\core;

use Yii;

final class Language
{
    /**
     * @var string
     */
    public $language;

    static public function supportedLanguages():array
    {
        if (isset(Yii::$app->params['languagesAvailable'])) {
            return Yii::$app->params['languagesAvailable'];
        } else {
            return ['en'];
        }
    }

    static public function languagesShort():array
    {
        $languageData = [
            'en' => 'EN',
            'pl' => 'PL',
        ];

        $languages = [];

        foreach (self::supportedLanguages() as $language) {
            if (isset($languageData[$language])) {
                $languages[$language] = $languageData[$language];
            }
        }

        return $languages;
    }

    static public function languagesLong():array
    {
        $languageData = [
            'en' => Yii::t('app', 'LANGUAGE_CODE_ENGLISH'),
            'pl' => Yii::t('app', 'LANGUAGE_CODE_POLISH'),
        ];

        $languages = [];

        foreach (self::supportedLanguages() as $language) {
            $languages[$language] = $languageData[$language];
        }

        return $languages;
    }

    /**
     * @param string $code
     * @return Language
     */
    static public function create($code):Language
    {
        $language = new Language();
        $language->language = $code;
        return $language;
    }

    /**
     * @return string|null
     */
    public function getName()
    {
        $names = self::languagesLong();
        return isset($names[$this->language]) ? $names[$this->language] : null;
    }

    /**
     * @return Language[]
     */
    static public function getLanguagesAsObjects():array
    {
        $languages = [];

        foreach (self::supportedLanguages() as $language) {
            $languages[] = self::create($language);
        }

        return $languages;
    }
}
