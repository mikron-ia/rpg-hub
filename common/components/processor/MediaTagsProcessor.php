<?php

namespace common\components\processor;

use common\models\Image;
use Yii;

class MediaTagsProcessor
{
    private const string IMAGE = 'Image';

    private static array $classQualifiedNames = [
        self::IMAGE => Image::class,
    ];

    public static function processMediaTags(string $text): string
    {
        $simplePatterns = [
            self::IMAGE => '|IMG:([a-z\d]{40})|',
        ];

        $errorMessages = [
            self::IMAGE => Yii::t('app', 'IMAGE_NOT_AVAILABLE'),
        ];

        foreach ($simplePatterns as $class => $simplePattern) {
            $foundInstances = [];
            preg_match_all($simplePattern, $text, $foundInstances, PREG_SET_ORDER);

            foreach ($foundInstances as $instance) {
                $match = $instance[0];
                $key = array_pop($instance);

                /** @var Image $object */
                $object = (self::$classQualifiedNames[$class])::findOne(['key' => $key]);

                if ($object) {
                    // todo At some point it may be useful to apply an interface; for now, just Images can be displayed
                    $replacement = $object->provideDisplayableImage(false);
                } else {
                    $replacement = '`' . $errorMessages[$class] . '`';
                }

                $text = str_replace($match, $replacement, $text);
            }
        }

        return $text;
    }
}
