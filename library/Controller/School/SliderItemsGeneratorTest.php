<?php

declare(strict_types=1);

namespace Municipio\Controller\School;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class SliderItemsGeneratorTest extends TestCase
{
    #[DataProvider('mediaItemsProvider')]
    public function testGenerateReturnsMediaItemsOnlyWhenThereAreMultipleItems(mixed $mediaItems, array $expected): void
    {
        // Arrange
        $mediaItemsGenerator = $this->createStub(ViewDataGeneratorInterface::class);
        $mediaItemsGenerator->method('generate')->willReturn($mediaItems);
        $generator = new SliderItemsGenerator($mediaItemsGenerator);

        // Act
        $result = $generator->generate();

        // Assert
        static::assertSame($expected, $result);
    }

    public static function mediaItemsProvider(): array
    {
        $twoImages = ['imageItems' => [['text' => 'First'], ['text' => 'Second']]];
        $imageAndVideo = ['imageItems' => [['text' => 'Image']], 'videoItems' => [['embed' => 'Video']]];

        return [
            'no media items' => [[], []],
            'not an array' => [null, []],
            'single image item' => [['imageItems' => [['text' => 'Image']]], []],
            'single video item' => [['videoItems' => [['embed' => 'Video']]], []],
            'two image items' => [$twoImages, $twoImages],
            'image and video item' => [$imageAndVideo, $imageAndVideo],
        ];
    }
}
