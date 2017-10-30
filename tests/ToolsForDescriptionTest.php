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
     * @param $data
     */
    public function testComplexConversion(array $data)
    {
        $linkBases = [
            'Character' => '/index.php/character/view/key=',
            'Group' => '/index.php/group/view/key=',
            'Story' => '/index.php/story/view/key=',
        ];

        $this->assertEquals($data['result'], $this->processKeys($data['text']));
    }

    /**
     * @return array
     */
    public function complexConversionDataProvider(): array
    {
        return [
            [
                [
                    'text' => '[Character\'s name](CH:184e5117955e384ca1e68dd731637bb8988782a1)',
                    'result' => '[Character\'s name](/index.php/character/view/key=184e5117955e384ca1e68dd731637bb8988782a1)'
                ],
            ],
        ];
    }
}
