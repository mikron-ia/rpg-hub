<?php

namespace common\models\tools;

use PHPUnit\Framework\TestCase;

class ToolsForLinkTagsTest extends TestCase
{
    use ToolsForLinkTags;

    /**
     * @dataProvider complexConversionDataProvider
     */
    public function testComplexConversion(string $text, string $result)
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
            'Correct character - short' => [
                '[Character\'s name](CH:184e5117955e384ca1e68dd731637bb8988782a1)',
                '[Character\'s name](/index.php/character/view/key=184e5117955e384ca1e68dd731637bb8988782a1)',
            ],
            'Correct group - short' => [
                '[Group\'s name](GR:184e5117955e384ca1e68dd731637bb8988782a1)',
                '[Group\'s name](/index.php/group/view/key=184e5117955e384ca1e68dd731637bb8988782a1)',
            ],
            'Correct story - short' => [
                '[Story\'s name](ST:184e5117955e384ca1e68dd731637bb8988782a1)',
                '[Story\'s name](/index.php/story/view/key=184e5117955e384ca1e68dd731637bb8988782a1)',
            ],
            'Correct character - long' => [
                '[Character\'s name](CHARACTER:184e5117955e384ca1e68dd731637bb8988782a1)',
                '[Character\'s name](/index.php/character/view/key=184e5117955e384ca1e68dd731637bb8988782a1)',
            ],
            'Correct group - long' => [
                '[Group\'s name](GROUP:184e5117955e384ca1e68dd731637bb8988782a1)',
                '[Group\'s name](/index.php/group/view/key=184e5117955e384ca1e68dd731637bb8988782a1)',
            ],
            'Correct story - long' => [
                '[Story\'s name](STORY:184e5117955e384ca1e68dd731637bb8988782a1)',
                '[Story\'s name](/index.php/story/view/key=184e5117955e384ca1e68dd731637bb8988782a1)',
            ],
            'Unprocessed - key is too long' => [
                '[Character\'s name](CH:184e5117955e384ca1e68dd731637bb8988782a15)',
                '[Character\'s name](CH:184e5117955e384ca1e68dd731637bb8988782a15)',
            ],
            'Unprocessed - key has invalid characters' => [
                '[Character\'s name](CH:184e5117A55e384ca1e68dd731637bb8988782a1)',
                '[Character\'s name](CH:184e5117A55e384ca1e68dd731637bb8988782a1)',
            ],
            'Unprocessed - wrong code' => [
                '[Character\'s name](CHA:184e5117955e384ca1e68dd731637bb8988782a1)',
                '[Character\'s name](CHA:184e5117955e384ca1e68dd731637bb8988782a1)',
            ],
            'Unprocessed - unnecessary space - beginning' => [
                '[Character\'s name]( CH:184e5117955e384ca1e68dd731637bb8988782a1)',
                '[Character\'s name]( CH:184e5117955e384ca1e68dd731637bb8988782a1)',
            ],
            'Unprocessed - unnecessary space - inside' => [
                '[Character\'s name](CH: 184e5117955e384ca1e68dd731637bb8988782a1)',
                '[Character\'s name](CH: 184e5117955e384ca1e68dd731637bb8988782a1)',
            ],
            'Unprocessed - unnecessary space - end' => [
                '[Character\'s name](CH:184e5117955e384ca1e68dd731637bb8988782a1 )',
                '[Character\'s name](CH:184e5117955e384ca1e68dd731637bb8988782a1 )',
            ],
            'Correct double - long - same type' => [
                '[Alpha](CH:184e5117955e384ca1e68dd731637bb8988782a1) [Beta](CH:184e5117955e384ca1e68dd731637bb8988782a1)',
                '[Alpha](/index.php/character/view/key=184e5117955e384ca1e68dd731637bb8988782a1) [Beta](/index.php/character/view/key=184e5117955e384ca1e68dd731637bb8988782a1)',
            ],
            'Correct double - long - different type' => [
                '[Alpha](CH:184e5117955e384ca1e68dd731637bb8988782a1) [Beta](GR:184e5117955e384ca1e68dd731637bb8988782a1)',
                '[Alpha](/index.php/character/view/key=184e5117955e384ca1e68dd731637bb8988782a1) [Beta](/index.php/group/view/key=184e5117955e384ca1e68dd731637bb8988782a1)',
            ],
        ];
    }

    /**
     * @dataProvider headerConversionDataProvider
     */
    public function testHeaderConversion(string $text, string $result)
    {
        $this->assertEquals($result, $this->expandHeaders($text));
    }

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
