<?php

$this->title = Yii::t('app', 'LABEL_MARKDOWN_HELP');

?>

<h1>Markdown help</h1>

The page's text areas use the basic `GitHub` markdown style, without forced newlines. Please note that this does
<strong>not</strong> apply to most one-line fields - using formatting there will be unsuccessful.

<h2>Commonly used elements of style</h2>

<ul>
    <li>
        Use empty lines between text to make paragraphs
        <ul>
            <li>This is due to lack of forced newlines</li>
        </ul>
    </li>
    <li>Use <code>*</code> or <code>_</code> for italics</li>
    <li>Use <code>**</code> for bold</li>
    <li>
        <code>*</code> at the beginning of the line makes an unnumbered list; use for <code>_</code> italics then, to
        avoid confusing the processor
    </li>
    <li>
        Use numbers with following dots for a numbered list; the processor will make them sequential most of the time,
        so using just <code>1.</code>'s will still result in a proper list
    </li>
    <li>Use backticks (<code>`</code>) for the <code>inline code effect</code></li>
    <li>
        Use <code>#</code>, <code>##</code> and so on for headers
        <ul>
            <li>
                Note: the page's processor will always add one header level to avoid multiple first-level headers; this
                means <code>#</code> will turn into a <code>h2</code>, not a <code>h1</code>
            </li>
        </ul>
    </li>
    <li>The entered text is HTML-encoded, thus using mathematical notation or pseudo-tags is safe</li>
</ul>

<h2>The tag system</h2>

The hub also contains its own extension to markdown in a form of a tag system. The following tags are valid:

<ul>
    <li><code>CHARACTER</code> / <code>CH</code> for characters</li>
    <li><code>GROUP</code> / <code>GR</code> for groups</li>
    <li><code>STORY</code> / <code>ST</code> for stories</li>
</ul>

They are always to be used in the <code>KEYWORD:UUID</code> format, for example <code>CH:302cb5e301d94b478a880b22ff0d4780</code>. If used without any wrap, they will create an anchor tag with the offical object's name as the content and the link to the object itself. If they are used in a markdown link wrap (e.g.
<code>[Name](CH:302cb5e301d94b478a880b22ff0d4780</code>)), the given name will be used as anchor's content instead. The requested object must be created first, otherwise the tag will not be rendered.

<strong>Note:</strong> this system works in descriptions, articles, and stories, but it does
<strong>not</strong> yet work in recaps. Use manual linking there.

<h2>Further reading</h2>

A good guide about using markdown is provided by
<a href="https://docs.github.com/en/get-started/writing-on-github/getting-started-with-writing-and-formatting-on-github/basic-writing-and-formatting-syntax">GitHub</a>.
