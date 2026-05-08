<?php

namespace common\components\service;

use common\dto\ImageDisplayObject;
use common\models\core\ImageDisplayMode;
use common\models\Image;
use common\models\ImageLink;

class ImageRotationService
{
    /**
     * @param array<ImageLink> $links
     * @param int $rolledNumber
     *
     * @return ImageLink|null
     */
    public static function chooseLink(array $links, int $rolledNumber): ?ImageLink
    {
        $total = 0;
        foreach ($links as $link) {
            $total += $link->display_weight;
            if ($rolledNumber < $total) {
                return $link;
            }
        }
        return null;
    }

    public static function calculateTotalWeight(array $links): int
    {
        return array_reduce($links, fn(int $carry, ImageLink $link) => $carry + $link->display_weight, 0);
    }

    public static function filterImageLinks(array $links, ImageDisplayMode $mode): array
    {
        return array_filter($links, fn(ImageLink $link) => $link->display_mode === $mode->value);
    }

    public static function makeDisplayObjectWithDimensions(Image $image, ImageLink $imageLink): ImageDisplayObject
    {
        return new ImageDisplayObject(
            url: $imageLink->link,
            alt: $image->alt,
            title: $image->title,
            height: $image->display_height,
            width: $image->display_width,
        );
    }

    public static function makeDisplayObjectWithoutDimensions(Image $image, ImageLink $imageLink): ImageDisplayObject
    {
        return new ImageDisplayObject(
            url: $imageLink->link,
            alt: $image->alt,
            title: $image->title,
            height: null,
            width: null,
        );
    }
}
