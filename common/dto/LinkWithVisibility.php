<?php

namespace common\dto;

use yii\helpers\Html;

final readonly class LinkWithVisibility
{
    public function __construct(
        public string $text,
        public string $url,
        public bool $isSecret,
    ) {
    }

    public function __toString(): string
    {
        return Html::a(
            text: htmlentities($this->text),
            url: $this->url,
        );
    }
}
