<?php

namespace common\components\service;

use common\models\core\ImageDisplayMode;
use common\models\Image;
use common\models\ImageLink;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class ImageRotationServiceTest extends TestCase
{
    private const string IMAGE_URL = 'http://localhost/test.jpg';
    
    #[DataProvider('chooseLinkDataProvider')]
    public function testChooseLink(array $weights, int $rolledNumber, ?int $expectedIndex): void
    {
        $links = array_map(
            static fn(int $weight): ImageLink => self::makeImageLink(displayWeight: $weight),
            $weights
        );

        $result = ImageRotationService::chooseLink($links, $rolledNumber);

        if ($expectedIndex === null) {
            self::assertNull($result);
            return;
        }

        self::assertSame($links[$expectedIndex], $result);
    }

    public function testChooseLinkEmpty(): void
    {
        self::assertNull(ImageRotationService::chooseLink([], 0));
    }

    public function testCalculateTotalWeightReturnsSumOfDisplayWeights(): void
    {
        $links = [
            self::makeImageLink(displayWeight: 10),
            self::makeImageLink(displayWeight: 25),
            self::makeImageLink(displayWeight: 5),
        ];

        self::assertSame(40, ImageRotationService::calculateTotalWeight($links));
    }

    public function testCalculateTotalWeightEmptyLinks(): void
    {
        self::assertSame(0, ImageRotationService::calculateTotalWeight([]));
    }

    public function testFilterImageLinksReturnsOnlyLinksMatchingDisplayMode(): void
    {
        $alwaysLinkA = self::makeImageLink(displayMode: ImageDisplayMode::Always);
        $alwaysLinkB = self::makeImageLink(displayMode: ImageDisplayMode::Always);
        $backupLink = self::makeImageLink(displayMode: ImageDisplayMode::Backup);
        $neverLink = self::makeImageLink(displayMode: ImageDisplayMode::Never);

        $result = ImageRotationService::filterImageLinks(
            [$alwaysLinkA, $backupLink, $alwaysLinkB, $neverLink],
            ImageDisplayMode::Always
        );

        self::assertSame([$alwaysLinkA, $alwaysLinkB], array_values($result));
    }

    public function testFilterImageLinksNoMatch(): void
    {
        $links = [
            self::makeImageLink(displayMode: ImageDisplayMode::Always),
            self::makeImageLink(displayMode: ImageDisplayMode::Never),
        ];

        self::assertSame([], ImageRotationService::filterImageLinks($links, ImageDisplayMode::Backup));
    }

    public function testMakeDisplayObjectWithDimensions(): void
    {
        $image = self::makeImage(
            alt: 'Test alt text',
            title: 'Test title text',
            displayHeight: 100,
            displayWidth: 200
        );
        $imageLink = self::makeImageLink();

        $result = ImageRotationService::makeDisplayObjectWithDimensions($image, $imageLink);

        self::assertSame(self::IMAGE_URL, $result->url);
        self::assertSame('Test alt text', $result->alt);
        self::assertSame('Test title text', $result->title);
        self::assertSame(100, $result->height);
        self::assertSame(200, $result->width);
    }

    public function testMakeDisplayObjectWithDimensionsNull(): void
    {
        $image = self::makeImage();
        $imageLink = self::makeImageLink();

        $result = ImageRotationService::makeDisplayObjectWithDimensions($image, $imageLink);

        self::assertSame(self::IMAGE_URL, $result->url);
        self::assertNull($result->alt);
        self::assertNull($result->title);
        self::assertNull($result->height);
        self::assertNull($result->width);
    }

    public function testMakeDisplayObjectWithoutDimensions(): void
    {
        $image = self::makeImage(
            alt: 'Test alt text',
            title: 'Test title text',
            displayHeight: 100,
            displayWidth: 200
        );
        $imageLink = self::makeImageLink(link: self::IMAGE_URL);

        $result = ImageRotationService::makeDisplayObjectWithoutDimensions($image, $imageLink);

        self::assertSame(self::IMAGE_URL, $result->url);
        self::assertSame('Test alt text', $result->alt);
        self::assertSame('Test title text', $result->title);
        self::assertNull($result->height);
        self::assertNull($result->width);
    }

    public static function chooseLinkDataProvider(): array
    {
        return [
            'firstAtStartOfFirstRange' => [[10, 20, 30], 0, 0],
            'firstAtEndOfFirstRange' => [[10, 20, 30], 9, 0],
            'secondAtStartOfSecondRange' => [[10, 20, 30], 10, 1],
            'secondAtEndOfSecondRange' => [[10, 20, 30], 29, 1],
            'thirdAtStartOfThirdRange' => [[10, 20, 30], 30, 2],
            'thirdAtEndOfThirdRange' => [[10, 20, 30], 59, 2],
            'nullWhenRollEqualsTotalWeight' => [[10, 20, 30], 60, null],
            'nullWhenRollExceedsTotalWeight' => [[10, 20, 30], 61, null],
        ];
    }

    private static function makeImageLink(
        string $link = self::IMAGE_URL,
        ImageDisplayMode $displayMode = ImageDisplayMode::Always,
        int $displayWeight = 100,
    ): ImageLink {
        $imageLink = new ImageLink();

        $imageLink->link = $link;
        $imageLink->display_mode = $displayMode->value;
        $imageLink->display_weight = $displayWeight;

        return $imageLink;
    }

    private static function makeImage(
        ?string $alt = null,
        ?string $title = null,
        ?int $displayHeight = null,
        ?int $displayWidth = null,
    ): Image {
        $image = new Image();

        $image->alt = $alt;
        $image->title = $title;
        $image->display_height = $displayHeight;
        $image->display_width = $displayWidth;

        return $image;
    }
}
