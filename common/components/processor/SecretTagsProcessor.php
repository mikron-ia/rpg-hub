<?php

namespace common\components\processor;

use common\models\Secret;
use Yii;

final class SecretTagsProcessor
{
    private const string ERROR_TEMPLATE = '<pre>%s</pre>';

    private const string PATTERN = '|SECRET:([a-z\d]{40})|';

    private const string OPERATOR_TEMPLATE =
        '<div class="secret-text-box">' .
        '<h4>%s</h4>' .
        '<div>%s</div>' .
        '</div>' .
        '<div class="secret-text-box secret-text-box-notes">'.
        '<div>%s</div>'.
        '<p class="secret-text-box-bestowed"><strong>%s:</strong> %s</p>' .
        '</div>';

    private const string USER_TEMPLATE =
        '<div class="secret-text-box">' .
        '<h4>%s</h4>' .
        '<div>%s</div>' .
        '</div>';

    public static function processSecretTagsForOperator(string $text): string
    {
        $foundInstances = [];
        preg_match_all(self::PATTERN, $text, $foundInstances, PREG_SET_ORDER);

        foreach ($foundInstances as $instance) {
            $match = $instance[0];
            $key = array_pop($instance);

            $secret = Secret::findOne(['key' => $key]);

            $replacement = !empty($secret)
                ? sprintf(
                    self::OPERATOR_TEMPLATE,
                    $secret->title,
                    $secret->getContentFormatted(),
                    $secret->getNotesFormatted(),
                    Yii::t('app', 'SECRET_BESTOWED_TO_LABEL'),
                    implode(' ', $secret->getBestowedListAsUsernames(true))
                )
                : sprintf(self::ERROR_TEMPLATE, Yii::t('app', 'SECRET_NOT_AVAILABLE'));

            $text = str_replace($match, $replacement, $text);
        }

        return $text;
    }

    public static function processSecretTagsForUser(string $text): string
    {
        $foundInstances = [];
        preg_match_all(self::PATTERN, $text, $foundInstances, PREG_SET_ORDER);

        foreach ($foundInstances as $instance) {
            $match = $instance[0];
            $key = array_pop($instance);

            $secret = Secret::findOne(['key' => $key]);

            $replacement = (
                !empty($secret) &&
                ($secret->bestowedList->hasBestowedFor(Yii::$app->user->id) || $secret->canUserControlYou())
            )
                ? sprintf(
                    self::USER_TEMPLATE,
                    $secret->title,
                    $secret->getContentFormatted()
                ) : '';

            $text = str_replace($match, $replacement, $text);
        }

        return $text;
    }
}
