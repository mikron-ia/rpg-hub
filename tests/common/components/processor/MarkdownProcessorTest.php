<?php

namespace common\components\processor;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use yii\helpers\Markdown;

final class MarkdownProcessorTest extends TestCase
{
    public function testBasicMarkdown(): void
    {
        $result = MarkdownProcessor::process('# Title');

        self::assertSame("<h1>Title</h1>\n", $result);
    }

    public function testFixGT(): void
    {
        self::assertSame(
            "<blockquote><p>quoted text</p>\n</blockquote>\n",
            MarkdownProcessor::process('&gt; quoted text')
        );
    }

    public function testFixIncorrectGT(): void
    {
        self::assertSame(
            "<blockquote><p>quoted text</p>\n</blockquote>\n",
            MarkdownProcessor::process('&GT; quoted text')
        );
    }

    public function testAddTableClasses(): void
    {
        $markdown = <<<TEXT
| Name       | Value        |
| ----       | -----        |
| NameValue  | ValueValue   |
TEXT;

        $result = MarkdownProcessor::process($markdown);

        self::assertStringContainsString(
            '<table class="table table-bordered table-striped table-from-markdown">',
            $result
        );
        self::assertStringNotContainsString('<table>', $result);
    }

    public function testNoTable(): void
    {
        $result = MarkdownProcessor::process('Plain text');

        self::assertSame("<p>Plain text</p>\n", $result);
        self::assertStringNotContainsString('table-from-markdown', $result);
    }

    #[DataProvider('markdownProvider')]
    public function testProcessLikeMarkdown(string $markdown): void
    {
        self::assertSame(Markdown::process($markdown), MarkdownProcessor::process($markdown));
    }

    public function testProcessLinks(): void
    {
        self::assertSame(
            'Plain text <a href="https://example.com">with a link</a> or <a href="https://example.com">two</a> inside',
            MarkdownProcessor::findAndFixLinksWithMarkdown('Plain text [with a link](https://example.com) or [two](https://example.com) inside')
        );
    }

    /**
     * @return array<string, array<string>>
     */
    public static function markdownProvider(): array
    {
        return [
            'ul' => ["- a\n- b"],
            'italic' => ['*Important* text'],
            'link' => ['[example](https://example.com)'],
        ];
    }
}
