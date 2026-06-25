<?php

namespace common\models\core;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class VisibilityTest extends TestCase
{
    #[DataProvider('visibilityValuesDataProvider')]
    public function testVisibilityNames(array $allowed, array $expectedUppercase, array $expectedLowercase): void
    {
        $this->assertSame($expectedUppercase, Visibility::visibilityNames($allowed));
        $this->assertSame($expectedLowercase, Visibility::visibilityNamesLowercase($allowed));
    }

    public function testGetName(): void
    {
        $this->assertSame('VISIBILITY_GM', Visibility::GameMaster->getName());
        $this->assertSame('VISIBILITY_FULL', Visibility::Full->getName());
    }

    public static function visibilityValuesDataProvider(): array
    {
        return [
            'empty' => [[], [], []],
            'simple' => [
                [Visibility::None],
                ['none' => 'VISIBILITY_NONE'],
                ['none' => 'VISIBILITY_NONE_LOWERCASE']
            ],
            'common' => [
                [Visibility::GameMaster, Visibility::Full],
                ['gm' => 'VISIBILITY_GM', 'full' => 'VISIBILITY_FULL'],
                ['gm' => 'VISIBILITY_GM_LOWERCASE', 'full' => 'VISIBILITY_FULL_LOWERCASE'],
            ],
            'all' => [
                [
                    Visibility::None,
                    Visibility::GameMaster,
                    Visibility::Designated,
                    Visibility::LoggedIn,
                    Visibility::Full
                ],
                [
                    'none' => 'VISIBILITY_NONE',
                    'gm' => 'VISIBILITY_GM',
                    'designated' => 'VISIBILITY_DESIGNATED',
                    'logged' => 'VISIBILITY_LOGGED',
                    'full' => 'VISIBILITY_FULL'
                ],
                [
                    'none' => 'VISIBILITY_NONE_LOWERCASE',
                    'gm' => 'VISIBILITY_GM_LOWERCASE',
                    'designated' => 'VISIBILITY_DESIGNATED_LOWERCASE',
                    'logged' => 'VISIBILITY_LOGGED_LOWERCASE',
                    'full' => 'VISIBILITY_FULL_LOWERCASE'
                ],
            ]
        ];
    }
}
