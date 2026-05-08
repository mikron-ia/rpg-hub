<?php

declare(strict_types=1);

namespace common\dto;

use yii\helpers\Html;

final readonly class ImageDisplayObject
{
    public function __construct(
        public string $url,
        public ?string $alt,
        public ?string $title,
        public ?int $height,
        public ?int $width,
    ) {
    }

    public function __toString(): string
    {
        $options = [];

        if ($this->alt) {
            $options['alt'] = $this->alt;
        }
        if ($this->height) {
            $options['height'] = $this->height;
        }
        if ($this->width) {
            $options['width'] = $this->width;
        }
        if ($this->title) {
            $options['title'] = $this->title;
        }

        return Html::img($this->url, $options);
    }
}
