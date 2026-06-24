<?php

namespace common\components\processor;

use yii\helpers\Markdown;

final class MarkdownProcessor
{
    /**
     * Basic processor for most applications
     */
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

    /**
     * A workaround method that finds and processes Markdown `[]()` links to `<a>` one by one
     *
     * This is a stopgap measure for late processing since Markdown processors don't work well (by design) on content
     * with HTML, and reworking the whole external data processing just for `CharacterSheet` objects is not practical
     */
    public static function findAndFixLinksWithMarkdown(string $text): string
    {
        $sources = [];
        $targets = [];

        $foundInstances = [];
        preg_match_all('|\[(.+?)]\((.+?)\)|', $text, $foundInstances, PREG_SET_ORDER);
        foreach ($foundInstances as $instance) {
            $source = $instance[0];
            $sources[] = $source;
            $targets[] = str_replace(['<p>', '</p>', PHP_EOL], '', Markdown::process(markdown: $source, flavor: 'gfm'));
        }

        return str_replace($sources, $targets, $text);
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
