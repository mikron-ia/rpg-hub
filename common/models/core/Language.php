<?php

namespace common\models\core;

use Yii;

/**
 * Class Language - wraps language support
 * @package common\models\core
 */
final class Language
{
    /**
     * @var string Language code
     */
    public $language;

    /**
     * Provides short names for languages
     * @return array
     */
    static public function languagesShort(): array
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

    /**
     * Lists supported languages
     * @return array
     */
    static public function supportedLanguages(): array
    {
        if (isset(Yii::$app->params['languagesAvailable'])) {
            return Yii::$app->params['languagesAvailable'];
        } else {
            return ['en'];
        }
    }

    /**
     * Provides languages as objects
     * @return Language[]
     */
    static public function getLanguagesAsObjects(): array
    {
        $languages = [];

        foreach (self::supportedLanguages() as $language) {
            $languages[] = self::create($language);
        }

        return $languages;
    }

    /**
     * Factory method
     * @param string $code
     * @return Language
     */
    static public function create($code): Language
    {
        $language = new Language();
        $language->language = $code;
        return $language;
    }

    /**
     * Provides language name
     * @return string|null
     */
    public function getName()
    {
        $names = self::languagesLong();
        return isset($names[$this->language]) ? $names[$this->language] : null;
    }

    /**
     * Provides long names for languages
     * @return array
     */
    static public function languagesLong(): array
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
}
