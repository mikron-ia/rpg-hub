<?php

namespace common\models\tools;

use common\components\processor\LinkTagsProcessor;
use common\components\processor\MarkdownProcessor;
use common\components\processor\MediaTagsProcessor;
use common\components\processor\SecretTagsProcessor;
use yii\helpers\Html;

trait ToolsForLinkTags
{
    /** @var array<string,string> */
    private static array $headerReplacements = [
        '|^##### |m' => '###### ',
        '|^#### |m' => '##### ',
        '|^### |m' => '#### ',
        '|^## |m' => '### ',
        '|^# |m' => '## ',
    ];

    /**
     * Processes text through all decorators
     */
    private function processAllInOrder(string $text): string
    {
        return $text
                |> LinkTagsProcessor::processKeys(...)
                |> $this->expandHeaders(...);
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

    private function processSecretTagsForOperator(string $text): string
    {
        return SecretTagsProcessor::processSecretTagsForOperator($text);
    }

    private function processSecretTagsForUser(string $text): string
    {
        return SecretTagsProcessor::processSecretTagsForUser($text);
    }

    private function formatText(?string $textToFormat, bool $processImages, bool $encode = true): string
    {
        $textForEncoding = $textToFormat ?? '';

        /**
         * Disabling encoding is needed to accommodate text pre-processing in some objects
         * Do not use this unless necessary for legacy reasons
         * @todo Hopefully fix in #576
         */
        return $textForEncoding
                |> (fn(string $text) => $encode ? Html::encode($textForEncoding) : $textForEncoding)
                |> MarkdownProcessor::process(...)
                |> (fn(string $text) => $processImages ? MediaTagsProcessor::processMediaTags($text) : $text);
    }
}
