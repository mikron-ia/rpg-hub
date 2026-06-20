<?php

namespace common\components\processor;

use yii\helpers\Markdown;

class MarkdownProcessor
{
    public static function process(string $textForMarkdown): string
    {
        return self::addClasses(
            Markdown::process(
                markdown: str_ireplace(
                    search: '&gt;',
                    replace: '>',
                    subject: $textForMarkdown
                ),
                flavor: 'gfm'
            )
        );
    }

    private static function addClasses(string $text): string
    {
        return str_replace(
            '<table>',
            '<table class="table table-bordered table-striped table-from-markdown">',
            $text
        );
    }
}
