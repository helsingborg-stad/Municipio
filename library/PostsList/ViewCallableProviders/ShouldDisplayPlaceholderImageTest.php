<?php

namespace Municipio\PostsList\ViewCallableProviders;

use ComponentLibrary\Integrations\Image\ImageInterface;
use Municipio\PostObject\NullPostObject;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class ShouldDisplayPlaceholderImageTest extends TestCase
{
    #[TestDox('returns true when given post has no image but any other post in list has an image')]
    public function testShouldDisplayPlaceholderImage(): void
    {
        $mockedImage = $this->createMock(ImageInterface::class);
        $postWithImage = new class($mockedImage) extends NullPostObject {
            public function __construct(
                private ImageInterface $image,
            ) {}

            public function getImage(null|int $width = null, null|int $height = null): null|ImageInterface
            {
                return $this->image;
            }
        };
        $postWithoutImage = new NullPostObject();
        $postsInPostsList = [$postWithImage, $postWithoutImage];
        $callable = (new ShouldDisplayPlaceholderImage(...$postsInPostsList))->getCallable();

        $this->assertTrue($callable($postWithoutImage));
    }

    #[TestDox('returns false when given post has an image')]
    public function testShouldNotDisplayPlaceholderImageWhenPostHasImage(): void
    {
        $mockedImage = $this->createMock(ImageInterface::class);
        $postWithImage = new class($mockedImage) extends NullPostObject {
            public function __construct(
                private ImageInterface $image,
            ) {}

            public function getImage(null|int $width = null, null|int $height = null): null|ImageInterface
            {
                return $this->image;
            }
        };
        $postsInPostsList = [$postWithImage];
        $callable = (new ShouldDisplayPlaceholderImage(...$postsInPostsList))->getCallable();

        $this->assertFalse($callable($postWithImage));
    }

    #[TestDox('returns false when no posts in list have an image')]
    public function testShouldNotDisplayPlaceholderImageWhenNoPostsHaveImage(): void
    {
        $postWithoutImage1 = new NullPostObject();
        $postWithoutImage2 = new NullPostObject();
        $postsInPostsList = [$postWithoutImage1, $postWithoutImage2];
        $callable = (new ShouldDisplayPlaceholderImage(...$postsInPostsList))->getCallable();

        $this->assertFalse($callable($postWithoutImage1));
    }
}
