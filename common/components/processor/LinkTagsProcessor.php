<?php

namespace common\components\processor;

use common\models\Article;
use common\models\Character;
use common\models\core\IsLinkable;
use common\models\Group;
use common\models\Location;
use common\models\Story;
use Yii;

final class LinkTagsProcessor
{
    private const string ARTICLE = 'Article';
    private const string CHARACTER = 'Character';
    private const string GROUP = 'Group';
    private const string LOCATION = 'Location';
    private const string STORY = 'Story';

    /** @var array<string> */
    private static array $availableClasses = [
        self::ARTICLE,
        self::CHARACTER,
        self::GROUP,
        self::LOCATION,
        self::STORY,
    ];

    /** @var array<string,string> */
    private static array $linkBases = [
        self::ARTICLE => '/index.php/article/view?key=',
        self::CHARACTER => '/index.php/character/view?key=',
        self::GROUP => '/index.php/group/view?key=',
        self::LOCATION => '/index.php/location/view?key=',
        self::STORY => '/index.php/story/view?key=',
    ];

    private static array $classQualifiedNames = [
        self::ARTICLE => Article::class,
        self::CHARACTER => Character::class,
        self::GROUP => Group::class,
        self::LOCATION => Location::class,
        self::STORY => Story::class,
    ];

    /**
     * Turns keys in the format of NN:key to []() Markdown links
     */
    public static function processKeys(string $text): string
    {
        return $text
                |> (fn(string $text) => self::processKeysInLinks($text, self::$linkBases)) // [name](CODE:key)
                |> (fn(string $text) => self::processKeysInTheOpen($text, self::$linkBases)); // CODE:key
    }

    /**
     * Turns keys in the format of [name](NN:key) and [name](code:key) to []() Markdown links
     *
     * @param string $text
     * @param string[] $linkBases
     *
     * @return string
     */
    public static function processKeysInLinks(string $text, array $linkBases): string
    {
        $complexPatterns = [
            self::ARTICLE => '|\[(.+?)]\(ART(ICLE)?:([a-z\d]{40})\)|',
            self::CHARACTER => '|\[(.+?)]\(CH(ARACTER)?:([a-z\d]{40})\)|',
            self::GROUP => '|\[(.+?)]\(GR(OUP)?:([a-z\d]{40})\)|',
            self::LOCATION => '|\[(.+?)]\(LOC(ATION)?:([a-z\d]{40})\)|',
            self::STORY => '|\[(.+?)]\(ST(ORY)?:([a-z\d]{40})\)|',
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
    private static function processKeysInTheOpen(string $text, array $linkBases): string
    {
        $simplePatterns = [
            self::CHARACTER => '|CH(ARACTER)?:([a-z\d]{40})|',
            self::GROUP => '|GR(OUP)?:([a-z\d]{40})|',
            self::STORY => '|ST(ORY)?:([a-z\d]{40})|',
            self::LOCATION => '|LOC(ATION)?:([a-z\d]{40})|',
            self::ARTICLE => '|ART(ICLE)?:([a-z\d]{40})|',
        ];

        $errorMessages = [
            self::CHARACTER => Yii::t('app', 'CHARACTER_NOT_AVAILABLE'),
            self::GROUP => Yii::t('app', 'GROUP_NOT_AVAILABLE'),
            self::STORY => Yii::t('app', 'STORY_NOT_AVAILABLE'),
            self::LOCATION => Yii::t('app', 'LOCATION_NOT_AVAILABLE'),
            self::ARTICLE => Yii::t('app', 'ARTICLE_NOT_AVAILABLE'),
        ];

        foreach ($simplePatterns as $class => $simplePattern) {
            $foundInstances = [];
            preg_match_all($simplePattern, $text, $foundInstances, PREG_SET_ORDER);

            foreach ($foundInstances as $instance) {
                $match = $instance[0];
                $key = array_pop($instance);

                /** @var IsLinkable|null $object */
                $object = (self::$classQualifiedNames[$class])::findOne(['key' => $key]);

                if ($object) {
                    $replacement = '[' . $object->getName() . '](' . $linkBases[$class] . $object->key . ')';
                } else {
                    $replacement = '`' . $errorMessages[$class] . '`';
                }

                $text = str_replace($match, $replacement, $text);
            }
        }

        return $text;
    }
}
