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
        $options = [
            'class' => 'img-responsive',
        ];

        if ($this->alt) {
            $options['alt'] = $this->alt;
        }

        if ($this->height || $this->width) {
            $styles = [];

            if ($this->height) {
                $styles[] = "height: {$this->height}px";
            }

            if ($this->width) {
                $styles[] = "width: {$this->width}px";
            }

            $options['style'] = implode('; ', $styles);
        }

        if ($this->title) {
            $options['title'] = $this->title;
        }

        return Html::img($this->url, $options);
    }
}
