<?php

namespace Municipio\PostsList\AnyPostHasImage;

use ComponentLibrary\Integrations\Image\ImageInterface;
use Municipio\PostObject\PostObjectInterface;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class AnyPostHasImageTest extends TestCase
{
    #[TestDox('returns true if any post has an image')]
    public function testCheckReturnsTrueIfAnyPostHasImage()
    {
        $postWithImage = $this->createMock(PostObjectInterface::class);
        $postWithImage->method('getImage')->willReturn($this->createMock(ImageInterface::class));

        $postWithoutImage = $this->createMock(PostObjectInterface::class);
        $postWithoutImage->method('getImage')->willReturn(null);

        $anypostHasImage = new AnyPostHasImage();

        $result = $anypostHasImage->check($postWithoutImage, $postWithImage);
        $this->assertTrue($result);
    }

    #[TestDox('returns false if no posts have an image')]
    public function testCheckReturnsFalseIfNoPostsHaveImage()
    {
        $postWithoutImage1 = $this->createMock(PostObjectInterface::class);
        $postWithoutImage1->method('getImage')->willReturn(null);

        $postWithoutImage2 = $this->createMock(PostObjectInterface::class);
        $postWithoutImage2->method('getImage')->willReturn(null);

        $anypostHasImage = new AnyPostHasImage();

        $result = $anypostHasImage->check($postWithoutImage1, $postWithoutImage2);
        $this->assertFalse($result);
    }
}
