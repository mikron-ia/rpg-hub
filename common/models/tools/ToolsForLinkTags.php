<?php

namespace common\models\tools;

use common\components\processor\MediaTagsProcessor;
use common\models\Character;
use common\models\Group;
use common\models\Location;
use common\models\Story;
use Yii;
use yii\helpers\Html;
use yii\helpers\Markdown;

trait ToolsForLinkTags
{
    private const string CHARACTER = 'Character';
    private const string GROUP = 'Group';
    private const string STORY = 'Story';
    private const string LOCATION = 'Location';

    /** @var array<string> */
    private static array $availableClasses = [
        self::CHARACTER,
        self::GROUP,
        self::STORY,
        self::LOCATION,
    ];

    /** @var array<string,string> */
    private static array $headerReplacements = [
        '|^##### |m' => '###### ',
        '|^#### |m' => '##### ',
        '|^### |m' => '#### ',
        '|^## |m' => '### ',
        '|^# |m' => '## ',
    ];

    /** @var array<string,string> */
    private static array $linkBases = [
        self::CHARACTER => '/index.php/character/view?key=',
        self::GROUP => '/index.php/group/view?key=',
        self::STORY => '/index.php/story/view?key=',
        self::LOCATION => '/index.php/location/view?key=',
    ];

    private static array $classQualifiedNames = [
        self::CHARACTER => Character::class,
        self::GROUP => Group::class,
        self::STORY => Story::class,
        self::LOCATION => Location::class,
    ];

    /**
     * Processes text through all decorators
     */
    private function processAllInOrder(string $text): string
    {
        return $this->expandHeaders($this->processKeys($text));
    }

    /**
     * Turns keys in the format of NN:key to []() Markdown links
     */
    private function processKeys(string $text): string
    {
        /* Solve cases of [name](CODE:key) */
        $textWithProcessedComplexKeys = $this->processKeysInLinks($text, self::$linkBases);

        /* Solve cases of CODE:key format */
        $textWithProcessedKeys = $this->processKeysInTheOpen($textWithProcessedComplexKeys, self::$linkBases);

        return $textWithProcessedKeys;
    }

    /**
     * Turns keys in the format of [name](NN:key) and [name](code:key) to []() Markdown links
     *
     * @param string $text
     * @param string[] $linkBases
     *
     * @return string
     */
    private function processKeysInLinks(string $text, array $linkBases): string
    {
        $complexPatterns = [
            self::CHARACTER => '|\[(.+?)]\(CH(ARACTER)?:([a-z\d]{40})\)|',
            self::GROUP => '|\[(.+?)]\(GR(OUP)?:([a-z\d]{40})\)|',
            self::STORY => '|\[(.+?)]\(ST(ORY)?:([a-z\d]{40})\)|',
            self::LOCATION => '|\[(.+?)]\(LOC(ATION)?:([a-z\d]{40})\)|',
        ];

        $complexReplacements = array_map(
            fn(string $class) => sprintf('[$1](%s$3)', $linkBases[$class]),
            self::$availableClasses
        );

        return preg_replace($complexPatterns, $complexReplacements, $text);
    }

    /**
     * Turns keys in the format of NN:key and code:key to []() Markdown links
     *
     * @param string $text
     * @param string[] $linkBases
     *
     * @return string
     */
    private function processKeysInTheOpen(string $text, array $linkBases): string
    {
        $simplePatterns = [
            self::CHARACTER => '|CH(ARACTER)?:([a-z\d]{40})|',
            self::GROUP => '|GR(OUP)?:([a-z\d]{40})|',
            self::STORY => '|ST(ORY)?:([a-z\d]{40})|',
            self::LOCATION => '|LOC(ATION)?:([a-z\d]{40})|',
        ];

        $errorMessages = [
            self::CHARACTER => Yii::t('app', 'CHARACTER_NOT_AVAILABLE'),
            self::GROUP => Yii::t('app', 'GROUP_NOT_AVAILABLE'),
            self::STORY => Yii::t('app', 'STORY_NOT_AVAILABLE'),
            self::LOCATION => Yii::t('app', 'LOCATION_NOT_AVAILABLE'),
        ];

        foreach ($simplePatterns as $class => $simplePattern) {
            $foundInstances = [];
            preg_match_all($simplePattern, $text, $foundInstances, PREG_SET_ORDER);

            foreach ($foundInstances as $instance) {
                $match = $instance[0];
                $key = array_pop($instance);

                $object = (self::$classQualifiedNames[$class])::findOne(['key' => $key]);

                if ($object) {
                    $replacement = '[' . $object->name . '](' . $linkBases[$class] . $object->key . ')';
                } else {
                    $replacement = '`' . $errorMessages[$class] . '`';
                }

                $text = str_replace($match, $replacement, $text);
            }
        }

        return $text;
    }

    /**
     * Expands headers by a step
     */
    private function expandHeaders(string $text): string
    {
        return preg_replace(array_keys(self::$headerReplacements), self::$headerReplacements, $text);
    }

    private function expandText(?string $textToFormat): string
    {
        return $this->processAllInOrder($textToFormat ?? '');
    }

    private function formatText(?string $textToFormat, bool $processImages): string
    {
        $processedText = Markdown::process(
            markdown: str_ireplace(
                search: '&gt;',
                replace: '>',
                subject: Html::encode($textToFormat ?? '')
            ),
            flavor: 'gfm'
        );

        return $processImages ? MediaTagsProcessor::processMediaTags($processedText) : $processedText;
    }
}
