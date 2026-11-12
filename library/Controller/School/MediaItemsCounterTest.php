<?php

declare(strict_types=1);

namespace Municipio\Controller\School;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class MediaItemsCounterTest extends TestCase
{
    #[DataProvider('mediaItemsProvider')]
    public function testCountReturnsNumberOfImageAndVideoItems(mixed $mediaItems, int $expected): void
    {
        // Act
        $result = MediaItemsCounter::count($mediaItems);

        // Assert
        static::assertSame($expected, $result);
    }

    public static function mediaItemsProvider(): array
    {
        return [
            'not an array' => [null, 0],
            'empty array' => [[], 0],
            'only images' => [['imageItems' => [[], []]], 2],
            'only videos' => [['videoItems' => [[]]], 1],
            'images and videos' => [['imageItems' => [[]], 'videoItems' => [[], []]], 3],
        ];
    }
}
