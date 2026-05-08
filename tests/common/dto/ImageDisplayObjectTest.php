<?php

namespace common\dto;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use yii\base\InvalidConfigException;

class ImageDisplayObjectTest extends TestCase
{
    private const string IMAGE_URL = 'http://localhost/test.jpg';
    
    #[DataProvider('toStringDataProvider')]
    public function testToStringValid(ImageDisplayObject $imageDisplayObject, string $expected): void
    {
        self::assertSame($expected, (string)$imageDisplayObject);
    }

    public function testToStringNoUrl(): void
    {
        $imageDisplayObject = new ImageDisplayObject(
            url: '',
            alt: null,
            title: null,
            height: null,
            width: null,
        );

        $this->expectException(InvalidConfigException::class);

        $result = (string)$imageDisplayObject;
    }

    public static function toStringDataProvider(): array
    {
        return [
            'all' => [
                new ImageDisplayObject(
                    url: self::IMAGE_URL,
                    alt: 'Image alt text',
                    title: 'Image title',
                    height: 100,
                    width: 200,
                ),
                '<img src="http://localhost/test.jpg" width="200" height="100" alt="Image alt text" title="Image title">',
            ],
            'url' => [
                new ImageDisplayObject(
                    url: self::IMAGE_URL,
                    alt: null,
                    title: null,
                    height: null,
                    width: null,
                ),
                '<img src="http://localhost/test.jpg" alt="">',
            ],
            'height' => [
                new ImageDisplayObject(
                    url: self::IMAGE_URL,
                    alt: null,
                    title: null,
                    height: 100,
                    width: null,
                ),
                '<img src="http://localhost/test.jpg" height="100" alt="">',
            ],
            'width' => [
                new ImageDisplayObject(
                    url: self::IMAGE_URL,
                    alt: null,
                    title: null,
                    height: null,
                    width: 200,
                ),
                '<img src="http://localhost/test.jpg" width="200" alt="">',
            ],
            'empty' => [
                new ImageDisplayObject(
                    url: self::IMAGE_URL,
                    alt: '',
                    title: '',
                    height: 0,
                    width: 0,
                ),
                '<img src="http://localhost/test.jpg" alt="">',
            ],
        ];
    }
}