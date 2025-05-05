<?php

namespace common\models\tools;

use Yii;
use yii\helpers\Html;
use yii\helpers\Markdown;

/**
 * Trait ToolsForDescription
 * @package common\models\tools
 */
trait ToolsForDescription
{
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
        'Character' => '/index.php/character/view?key=',
        'Group' => '/index.php/group/view?key=',
        'Story' => '/index.php/story/view?key=',
    ];

    /**
     * Processes text thorough all decorators
     */
    private function processAllInOrder(string $text): string
    {
        return $this->expandHeaders($this->processKeys($text));
    }

    /**
     * Turns keys in format of NN:key to []() Markdown links
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
     * Turns keys in format of [name](NN:key) and [name](code:key) to []() Markdown links
     *
     * @param string $text
     * @param string[] $linkBases
     *
     * @return string
     */
    private function processKeysInLinks(string $text, array $linkBases): string
    {
        $complexPatterns = [
            'Character' => '|\[(.+)\]\(CH(ARACTER)?:([a-z\d]{40})\)|',
            'Group' => '|\[(.+)\]\(GR(OUP)?:([a-z\d]{40})\)|',
            'Story' => '|\[(.+)\]\(ST(ORY)?:([a-z\d]{40})\)|',
        ];

        $complexReplacements = [
            'Character' => '[$1](' . $linkBases['Character'] . '$3)',
            'Group' => '[$1](' . $linkBases['Group'] . '$3)',
            'Story' => '[$1](' . $linkBases['Story'] . '$3)',
        ];

        return preg_replace($complexPatterns, $complexReplacements, $text);
    }

    /**
     * Turns keys in format of NN:key and code:key to []() Markdown links
     *
     * @param string $text
     * @param string[] $linkBases
     *
     * @return string
     */
    private function processKeysInTheOpen(string $text, array $linkBases): string
    {
        $simplePatterns = [
            'Character' => '|CH(ARACTER)?:([a-z\d]{40})|',
            'Group' => '|GR(OUP)?:([a-z\d]{40})|',
            'Story' => '|ST(ORY)?:([a-z\d]{40})|',
        ];

        $errorMessages = [
            'Character' => Yii::t('app', 'CHARACTER_NOT_AVAILABLE'),
            'Group' => Yii::t('app', 'GROUP_NOT_AVAILABLE'),
            'Story' => Yii::t('app', 'STORY_NOT_AVAILABLE'),
        ];

        foreach ($simplePatterns as $class => $simplePattern) {
            $foundInstances = [];
            preg_match_all($simplePattern, $text, $foundInstances, PREG_SET_ORDER);

            foreach ($foundInstances as $instance) {
                $className = 'common\models\\' . $class;
                $match = $instance[0];
                $key = array_pop($instance);

                $object = ($className)::findOne(['key' => $key]);

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

    private function formatText(string $textToFormat): string
    {
        return Markdown::process(str_ireplace('&gt;', '>', Html::encode($textToFormat)), 'gfm');
    }
}
