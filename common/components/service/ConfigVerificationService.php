<?php

namespace common\components\service;

class ConfigVerificationService
{
    private static array $expectedNumberedValues = ['number0', 'number1', 'number2', 'number3', 'number4'];

    private static array $importanceConfigKeys = [
        'IMPORTANCE_CATEGORY_IMPORTANCE_EXTREME_VALUE',
        'IMPORTANCE_CATEGORY_IMPORTANCE_HIGH_VALUE',
        'IMPORTANCE_CATEGORY_IMPORTANCE_MEDIUM_VALUE',
        'IMPORTANCE_CATEGORY_IMPORTANCE_LOW_VALUE',
        'IMPORTANCE_CATEGORY_IMPORTANCE_NONE_VALUE',
        'IMPORTANCE_NEW_VALUE',
        'IMPORTANCE_UPDATED_VALUE',
        'IMPORTANCE_DEFAULT_VALUE',
        'IMPORTANCE_ASSOCIATED_VALUE',
        'IMPORTANCE_UNASSOCIATED_VALUE',
        'IMPORTANCE_DATE_INITIAL_VALUE',
        'IMPORTANCE_DATE_DIVIDER_VALUE',
    ];

    private static array $frontFormattingConfigKeys = [
        'INDEX_BOX_TITLE_MAXIMUM_WORDS_WITH_TAGS',
        'INDEX_BOX_SUBTITLE_MAXIMUM_WORDS_WITH_TAGS',
        'INDEX_BOX_TITLE_MAXIMUM_WORDS_WITHOUT_TAGS',
        'INDEX_BOX_SUBTITLE_MAXIMUM_WORDS_WITHOUT_TAGS',
    ];

    public static function checkApiConfig(?string $apiKey): bool
    {
        return self::isNotEmptyString($apiKey);
    }

    public static function checkUriConfig(?string $uriBack, ?string $uriFront): bool
    {
        return self::isNotEmptyString($uriBack) && self::isNotEmptyString($uriFront);
    }

    /**
     * @param array<string,string> $variables
     * @return array<string>
     */
    public static function checkForNumberPlaceholders(array $variables): array
    {
        $faultyKeys = [];
        foreach ($variables as $key => $variable) {
            if (!array_reduce(
                self::$expectedNumberedValues,
                fn($carry, $numberedValue): bool => $carry && str_contains($variable, $numberedValue),
                true
            )) {
                $faultyKeys[] = $key;
            }
        }
        return $faultyKeys;
    }

    public static function checkImportanceConfigKeys(): array
    {
        return self::checkKeysPresence(self::$importanceConfigKeys);
    }

    public static function checkFrontFormattingValues(): array
    {
        return self::checkKeysPresence(self::$frontFormattingConfigKeys);
    }

    private static function isNotEmptyString(?string $string): bool
    {
        return !empty(trim($string));
    }

    private static function checkKeysPresence(array $keys): array
    {
        $faultyKeys = [];
        foreach ($keys as $key) {
            if (getenv($key) === false) {
                $faultyKeys[] = $key;
            }
        }
        return $faultyKeys;
    }
}
