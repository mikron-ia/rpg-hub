<?php

namespace common\components\service;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ConfigVerificationServiceTest extends TestCase
{
    #[DataProvider('checkApiConfigDataProvider')]
    public function testCheckApiConfig(?string $apiKey, bool $expected): void
    {
        self::assertSame($expected, ConfigVerificationService::checkApiConfig($apiKey));
    }

    #[DataProvider('checkUriConfigDataProvider')]
    public function testCheckUriConfig(?string $uriBack, ?string $uriFront, bool $expected): void
    {
        self::assertSame($expected, ConfigVerificationService::checkUriConfig($uriBack, $uriFront));
    }

    #[DataProvider('checkForNumberPlaceholdersDataProvider')]
    public function testCheckForNumberPlaceholders(array $variables, array $expected): void
    {
        self::assertSame($expected, ConfigVerificationService::checkForNumberPlaceholders($variables));
    }

    #[DataProvider('checkImportanceConfigKeysDataProvider')]
    public function testCheckImportanceConfigKeys(array $environment, array $expected): void
    {
        self::assertSame($expected, ConfigVerificationService::checkImportanceConfigKeys($environment));
    }

    #[DataProvider('checkFrontFormattingValuesDataProvider')]
    public function testCheckFrontFormattingValues(array $environment, array $expected): void
    {
        self::assertSame($expected, ConfigVerificationService::checkFrontFormattingValues($environment));
    }

    public static function checkApiConfigDataProvider(): array
    {
        return [
            'valid' => ['abc123', true],
            'empty' => ['', false],
            'space' => [' ', false],
            'null' => [null, false],
        ];
    }

    public static function checkUriConfigDataProvider(): array
    {
        return [
            'valid' => ['https://cms.rpg-hub', 'https://front.rpg-hub', true],
            'emptyBack' => ['', 'https://front.rpg-hub', false],
            'emptyFront' => ['https://cms.rpg-hub', '', false],
            'spacesOnlyBack' => [' ', 'https://front.rpg-hub', false],
            'spacesOnlyFront' => ['https://cms.rpg-hub', ' ', false],
            'nullBack' => [null, 'https://front.rpg-hub', false],
            'nullFront' => ['https://cms.rpg-hub', null, false],
            'null' => [null, null, false],
        ];
    }

    public static function checkForNumberPlaceholdersDataProvider(): array
    {
        return [
            'allValid' => [
                [
                    'first' => 'first-key-base-{number0}-{number1}-{number2}-{number3}-{number4}',
                    'second' => 'second-key-base-{number0}-{number1}-{number2}-{number3}-{number4}',
                    'third' => 'second-key-base-number0-number2-number1-number4-number3',
                ],
                [],
            ],
            'oneEmpty' => [
                [
                    'first' => 'first-key-base-{number0}-{number1}-{number2}-{number3}-{number4}',
                    'second' => 'second-key-base-{number0}-{number1}-{number2}-{number3}',
                ],
                ['second'],
            ],
            'both' => [
                [
                    'first' => 'first-key-base-{number0}-{number1}-{number2}-{number3}',
                    'second' => 'second-key-base-{number1}-{number2}-{number3}-{number4}',
                ],
                ['first', 'second'],
            ],
            'empty' => [
                [],
                [],
            ],
        ];
    }

    public static function checkImportanceConfigKeysDataProvider(): array
    {
        return [
            'all' => [
                [
                    'IMPORTANCE_CATEGORY_IMPORTANCE_EXTREME_VALUE' => '8',
                    'IMPORTANCE_CATEGORY_IMPORTANCE_HIGH_VALUE' => '4',
                    'IMPORTANCE_CATEGORY_IMPORTANCE_MEDIUM_VALUE' => '2',
                    'IMPORTANCE_CATEGORY_IMPORTANCE_LOW_VALUE' => '1',
                    'IMPORTANCE_CATEGORY_IMPORTANCE_NONE_VALUE' => '0',
                    'IMPORTANCE_NEW_VALUE' => '16',
                    'IMPORTANCE_UPDATED_VALUE' => '8',
                    'IMPORTANCE_DEFAULT_VALUE' => '0',
                    'IMPORTANCE_ASSOCIATED_VALUE' => '2',
                    'IMPORTANCE_UNASSOCIATED_VALUE' => '0',
                    'IMPORTANCE_DATE_INITIAL_VALUE' => '8',
                    'IMPORTANCE_DATE_DIVIDER_VALUE' => '2',
                ],
                [],
            ],
            'some' => [
                [
                    'IMPORTANCE_CATEGORY_IMPORTANCE_EXTREME_VALUE' => '16',
                    'IMPORTANCE_CATEGORY_IMPORTANCE_HIGH_VALUE' => '8',
                    'IMPORTANCE_NEW_VALUE' => '16',
                    'IMPORTANCE_UPDATED_VALUE' => '8',
                    'IMPORTANCE_DEFAULT_VALUE' => '0',
                    'IMPORTANCE_ASSOCIATED_VALUE' => '2',
                    'IMPORTANCE_DATE_INITIAL_VALUE' => '2',
                ],
                [
                    'IMPORTANCE_CATEGORY_IMPORTANCE_MEDIUM_VALUE',
                    'IMPORTANCE_CATEGORY_IMPORTANCE_LOW_VALUE',
                    'IMPORTANCE_CATEGORY_IMPORTANCE_NONE_VALUE',
                    'IMPORTANCE_UNASSOCIATED_VALUE',
                    'IMPORTANCE_DATE_DIVIDER_VALUE',
                ],
            ],
            'none' => [
                [],
                [
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
                ],
            ],
        ];
    }

    public static function checkFrontFormattingValuesDataProvider(): array
    {
        return [
            'all' => [
                [
                    'INDEX_BOX_TITLE_MAXIMUM_WORDS_WITH_TAGS' => '8',
                    'INDEX_BOX_SUBTITLE_MAXIMUM_WORDS_WITH_TAGS' => '12',
                    'INDEX_BOX_TITLE_MAXIMUM_WORDS_WITHOUT_TAGS' => '12',
                    'INDEX_BOX_SUBTITLE_MAXIMUM_WORDS_WITHOUT_TAGS' => '24',
                ],
                [],
            ],
            'some' => [
                [
                    'INDEX_BOX_TITLE_MAXIMUM_WORDS_WITH_TAGS' => '8',
                    'INDEX_BOX_TITLE_MAXIMUM_WORDS_WITHOUT_TAGS' => '12',
                ],
                [
                    'INDEX_BOX_SUBTITLE_MAXIMUM_WORDS_WITH_TAGS',
                    'INDEX_BOX_SUBTITLE_MAXIMUM_WORDS_WITHOUT_TAGS',
                ],
            ],
            'none' => [
                [],
                [
                    'INDEX_BOX_TITLE_MAXIMUM_WORDS_WITH_TAGS',
                    'INDEX_BOX_SUBTITLE_MAXIMUM_WORDS_WITH_TAGS',
                    'INDEX_BOX_TITLE_MAXIMUM_WORDS_WITHOUT_TAGS',
                    'INDEX_BOX_SUBTITLE_MAXIMUM_WORDS_WITHOUT_TAGS',
                ],
            ],
        ];
    }
}
