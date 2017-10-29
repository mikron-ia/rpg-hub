<?php

namespace common\models\tools;


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
        $linkBases = [
            'Character' => '/index.php/character/view/key=',
            'Group' => '/index.php/group/view/key=',
            'Story' => '/index.php/story/view/key=',
        ];

        $complexPatterns = [
            'Character' => '|\[(.+)\]\(CH:([a-z\d]{40})\)|',
            'Group' => '|\[(.+)\]\(GR:([a-z\d]{40})\)|',
            'Story' => '|\[(.+)\]\(ST:([a-z\d]{40})\)|',
        ];

        $complexReplacements = [
            'Character' => '[$1](' . $linkBases['Character'] . '$2)',
            'Group' => '[$1](' . $linkBases['Group'] . '$2)',
            'Story' => '[$1](' . $linkBases['Story'] . '$2)',
        ];

        $textWithProcessedComplexKeys = preg_replace($complexPatterns, $complexReplacements, $text);

        $simplePatterns = [
            'Character' => '|CH:([a-z\d]{40})|',
            'Group' => '|GR:([a-z\d]{40})|',
            'Story' => '|ST:([a-z\d]{40})|',
        ];

        $textWithProcessedKeys = $textWithProcessedComplexKeys;

        foreach ($simplePatterns as $class => $simplePattern) {
            $foundInstances = [];

            preg_match_all($simplePattern, $textWithProcessedKeys, $foundInstances, PREG_SET_ORDER);

            foreach ($foundInstances as $instance) {
                $className = 'common\models\\' . $class;
                $object = ($className)::findOne(['key' => $instance[1]]);

                if ($object) {
                    $replacement = '[' . $object->name . '](' . $linkBases[$class] . $object->key . ')';
                } else {
                    $replacement = '`' . \Yii::t('app', 'CHARACTER_NOT_FOUND {key}', ['key' => $instance[1]]) . '`';
                }

                $textWithProcessedKeys = str_replace(
                    $instance[0],
                    $replacement,
                    $textWithProcessedKeys
                );
            }
        }

        return $textWithProcessedKeys;
    }
}
