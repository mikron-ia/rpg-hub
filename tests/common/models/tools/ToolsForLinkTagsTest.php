<?php

namespace common\models\tools;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ToolsForLinkTagsTest extends TestCase
{
    use ToolsForLinkTags;

    #[DataProvider('headerConversionDataProvider')]
    public function testHeaderConversion(string $text, string $result)
    {
        $this->assertEquals($result, $this->expandHeaders($text));
    }

    public static function headerConversionDataProvider(): array
    {
        return [
            ['# Lorem ipsum', '## Lorem ipsum'],
            ['## Lorem ipsum', '### Lorem ipsum'],
            ['### Lorem ipsum', '#### Lorem ipsum'],
            ['#### Lorem ipsum', '##### Lorem ipsum'],
            ['##### Lorem ipsum', '###### Lorem ipsum'],
            ['###### Lorem ipsum', '###### Lorem ipsum'],
            ['#Lorem ipsum', '#Lorem ipsum'],
            [' # Lorem ipsum', ' # Lorem ipsum'],
            ['Lorem ipsum' . PHP_EOL . '# Lorem ipsum', 'Lorem ipsum' . PHP_EOL . '## Lorem ipsum'],
            [
                '# Lorem ipsum' . PHP_EOL . '## Lorem ipsum' . PHP_EOL . '# Lorem ipsum',
                '## Lorem ipsum' . PHP_EOL . '### Lorem ipsum' . PHP_EOL . '## Lorem ipsum'
            ],
            [
                '# Lorem ipsum' . PHP_EOL . ' ## Lorem ipsum' . PHP_EOL . '# Lorem ipsum',
                '## Lorem ipsum' . PHP_EOL . ' ## Lorem ipsum' . PHP_EOL . '## Lorem ipsum'
            ],
        ];
    }
}
