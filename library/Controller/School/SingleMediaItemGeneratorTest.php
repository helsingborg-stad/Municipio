<?php

declare(strict_types=1);

namespace Municipio\Controller\School;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class SingleMediaItemGeneratorTest extends TestCase
{
    public function testCanBeInstantiated(): void
    {
        // Act
        $generator = new SingleMediaItemGenerator($this->getMediaItemsGenerator([]));

        // Assert
        static::assertInstanceOf(SingleMediaItemGenerator::class, $generator);
    }

    public function testGenerateReturnsImageItemWhenThereIsOnlyOneImage(): void
    {
        // Arrange
        $imageItem = ['text' => 'Image caption'];
        $generator = new SingleMediaItemGenerator($this->getMediaItemsGenerator(['imageItems' => [$imageItem]]));

        // Act
        $result = $generator->generate();

        // Assert
        static::assertSame($imageItem, $result);
    }

    public function testGenerateReturnsVideoItemWhenThereIsOnlyOneVideo(): void
    {
        // Arrange
        $videoItem = ['embed' => '<iframe></iframe>'];
        $generator = new SingleMediaItemGenerator($this->getMediaItemsGenerator(['videoItems' => [$videoItem]]));

        // Act
        $result = $generator->generate();

        // Assert
        static::assertSame($videoItem, $result);
    }

    #[DataProvider('noSingleMediaItemProvider')]
    public function testGenerateReturnsNullWhenThereIsNotExactlyOneMediaItem(mixed $mediaItems): void
    {
        // Arrange
        $generator = new SingleMediaItemGenerator($this->getMediaItemsGenerator($mediaItems));

        // Act
        $result = $generator->generate();

        // Assert
        static::assertNull($result);
    }

    public static function noSingleMediaItemProvider(): array
    {
        return [
            'no media items' => [[]],
            'not an array' => [null],
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
