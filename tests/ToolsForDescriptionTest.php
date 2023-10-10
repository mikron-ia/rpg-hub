<?php

namespace tests;

use common\models\tools\ToolsForDescription;
use PHPUnit\Framework\TestCase;

class ToolsForDescriptionTest extends TestCase
{
    use ToolsForDescription;

    /**
     * @test
     * @dataProvider complexConversionDataProvider
     * @param $text
     * @param $result
     */
    public function testComplexConversion($text, $result)
    {
        $linkBases = [
            'Character' => '/index.php/character/view/key=',
            'Group' => '/index.php/group/view/key=',
            'Story' => '/index.php/story/view/key=',
        ];

        $this->assertEquals($result, $this->processKeysInLinks($text, $linkBases));
    }

    static public function complexConversionDataProvider(): array
    {
        return [
            [
                /* Correct character */
                '[Character\'s name](CH:184e5117955e384ca1e68dd731637bb8988782a1)',
                '[Character\'s name](/index.php/character/view/key=184e5117955e384ca1e68dd731637bb8988782a1)'
            ],
            [
                /* Correct group */
                '[Group\'s name](GR:184e5117955e384ca1e68dd731637bb8988782a1)',
                '[Group\'s name](/index.php/group/view/key=184e5117955e384ca1e68dd731637bb8988782a1)'

            ],
            [
                /* Correct story */
                '[Story\'s name](ST:184e5117955e384ca1e68dd731637bb8988782a1)',
                '[Story\'s name](/index.php/story/view/key=184e5117955e384ca1e68dd731637bb8988782a1)'
            ],
            [
                /* Correct character */
                '[Character\'s name](CHARACTER:184e5117955e384ca1e68dd731637bb8988782a1)',
                '[Character\'s name](/index.php/character/view/key=184e5117955e384ca1e68dd731637bb8988782a1)'
            ],
            [
                /* Correct group */
                '[Group\'s name](GROUP:184e5117955e384ca1e68dd731637bb8988782a1)',
                '[Group\'s name](/index.php/group/view/key=184e5117955e384ca1e68dd731637bb8988782a1)'
            ],
            [
                /* Correct story */
                '[Story\'s name](STORY:184e5117955e384ca1e68dd731637bb8988782a1)',
                '[Story\'s name](/index.php/story/view/key=184e5117955e384ca1e68dd731637bb8988782a1)'
            ],
            [
                /* Unprocessed - key is too long */
                '[Character\'s name](CH:184e5117955e384ca1e68dd731637bb8988782a15)',
                '[Character\'s name](CH:184e5117955e384ca1e68dd731637bb8988782a15)'
            ],
            [
                /* Unprocessed - key has invalid characters */
                '[Character\'s name](CH:184e5117A55e384ca1e68dd731637bb8988782a1)',
                '[Character\'s name](CH:184e5117A55e384ca1e68dd731637bb8988782a1)'
            ],
            [
                /* Unprocessed - wrong code */
                '[Character\'s name](CHA:184e5117955e384ca1e68dd731637bb8988782a1)',
                '[Character\'s name](CHA:184e5117955e384ca1e68dd731637bb8988782a1)'
            ],
            [
                /* Unprocessed - unnecessary space */
                '[Character\'s name](CH: 184e5117955e384ca1e68dd731637bb8988782a1)',
                '[Character\'s name](CH: 184e5117955e384ca1e68dd731637bb8988782a1)'
            ],
            [
                /* Unprocessed - unnecessary space */
                '[Character\'s name](CH:184e5117955e384ca1e68dd731637bb8988782a1 )',
                '[Character\'s name](CH:184e5117955e384ca1e68dd731637bb8988782a1 )',
            ],
        ];
    }

    /**
     * @test
     * @dataProvider headerConversionDataProvider
     * @param string $text
     * @param string $result
     */
    public function testHeaderConversion(string $text, string $result)
    {
        $this->assertEquals($result, $this->expandHeaders($text));
    }

    /**
     * @return array
     */
    static public function headerConversionDataProvider(): array
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
