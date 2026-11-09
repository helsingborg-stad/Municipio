<?php

declare(strict_types=1);

namespace Municipio\Controller\School;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class SliderItemsGeneratorTest extends TestCase
{
    public function testCanBeInstantiated(): void
    {
        // Act
        $generator = new SliderItemsGenerator($this->getMediaItemsGenerator([]));

        // Assert
        static::assertInstanceOf(SliderItemsGenerator::class, $generator);
    }

    #[DataProvider('noSliderProvider')]
    public function testGenerateReturnsEmptyArrayWhenThereIsOneOrLessMediaItem(mixed $mediaItems): void
    {
        // Arrange
        $generator = new SliderItemsGenerator($this->getMediaItemsGenerator($mediaItems));

        // Act
        $result = $generator->generate();

        // Assert
        static::assertSame([], $result);
    }

    public static function noSliderProvider(): array
    {
        return [
            'no media items' => [[]],
            'not an array' => [null],
            'single image item' => [['imageItems' => [['text' => 'Image']]]],
            'single video item' => [['videoItems' => [['embed' => '<iframe></iframe>']]]],
        ];
    }

    #[DataProvider('sliderProvider')]
    public function testGenerateReturnsMediaItemsWhenThereAreMultipleMediaItems(array $mediaItems): void
    {
        // Arrange
        $generator = new SliderItemsGenerator($this->getMediaItemsGenerator($mediaItems));

        // Act
        $result = $generator->generate();

        // Assert
        static::assertSame($mediaItems, $result);
    }

    public static function sliderProvider(): array
    {
        return [
            'two image items' => [['imageItems' => [['text' => 'First'], ['text' => 'Second']]]],
            'one image and one video item' => [[
                'imageItems' => [['text' => 'Image']],
                'videoItems' => [['embed' => '<iframe></iframe>']],
            ]],
        ];
    }

    private function getMediaItemsGenerator(mixed $mediaItems): ViewDataGeneratorInterface
    {
        return new class($mediaItems) implements ViewDataGeneratorInterface {
            public function __construct(
                private mixed $mediaItems,
            ) {}

            public function generate(): mixed
            {
                return $this->mediaItems;
            }
        };
    }
}
