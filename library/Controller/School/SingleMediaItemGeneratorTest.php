<?php

declare(strict_types=1);

namespace Municipio\Controller\School;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class SingleMediaItemGeneratorTest extends TestCase
{
    #[DataProvider('mediaItemsProvider')]
    public function testGenerateReturnsItemOnlyWhenThereIsExactlyOneItem(mixed $mediaItems, ?array $expected): void
    {
        // Arrange
        $mediaItemsGenerator = $this->createStub(ViewDataGeneratorInterface::class);
        $mediaItemsGenerator->method('generate')->willReturn($mediaItems);
        $generator = new SingleMediaItemGenerator($mediaItemsGenerator);

        // Act
        $result = $generator->generate();

        // Assert
        static::assertSame($expected, $result);
    }

    public static function mediaItemsProvider(): array
    {
        $image = ['text' => 'Image caption'];
        $video = ['embed' => '<iframe></iframe>'];

        return [
            'only one image' => [['imageItems' => [$image]], $image],
            'only one video' => [['videoItems' => [$video]], $video],
            'no media items' => [[], null],
            'not an array' => [null, null],
            'two images' => [['imageItems' => [$image, $image]], null],
            'image and video' => [['imageItems' => [$image], 'videoItems' => [$video]], null],
        ];
    }
}
