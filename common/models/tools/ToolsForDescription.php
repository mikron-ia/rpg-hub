<?php

namespace common\models\tools;

use Yii;

/**
 * Trait ToolsForDescription
 * @package common\models\tools
 */
trait ToolsForDescription
{
    /**
     * Processes text thorough all decorators
     * @param string $text
     * @return string
     */
    private function processAllInOrder(string $text): string
    {
        return $this->processKeys($text);
    }

    /**
     * Turns keys in format of NN:key to []() markdown links
     * @param string $text
     * @return string
     */
    private function processKeys(string $text): string
    {
        /* Define bases */
        $linkBases = [
            'Character' => '/index.php/character/view/key=',
            'Group' => '/index.php/group/view/key=',
            'Story' => '/index.php/story/view/key=',
        ];

        /* Solve cases of [name](CODE:key) */
        $textWithProcessedComplexKeys = $this->processKeysInLinks($text, $linkBases);

        /* Solve cases of CODE:key format */
        $textWithProcessedKeys = $this->processKeysInTheOpen($textWithProcessedComplexKeys, $linkBases);

        return $textWithProcessedKeys;
    }

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

        $textWithProcessedComplexKeys = preg_replace($complexPatterns, $complexReplacements, $text);

        return $textWithProcessedComplexKeys;
    }

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
     * @param string $text
     * @return string
     */
    private function expandHeaders(string $text): string
    {
        $replacements = [
            '|^##### |m' => '###### ',
            '|^#### |m' => '##### ',
            '|^### |m' => '#### ',
            '|^## |m' => '### ',
            '|^# |m' => '## ',
        ];

        $text = preg_replace(array_keys($replacements), $replacements, $text);

        return $text;
    }
}
