<?php

declare(strict_types=1);

namespace Municipio\PostObject\Decorators;

use ComponentLibrary\Integrations\Image\ImageInterface;
use Municipio\PostObject\NullPostObject;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class PostObjectWithImageVisibilityTest extends TestCase
{
    #[TestDox('returns the featured image when image display is enabled')]
    public function testReturnsImageWhenDisplayIsEnabled(): void
    {
        $image = $this->createMock(ImageInterface::class);
        $post = new class ($image) extends NullPostObject {
            public function __construct(private ImageInterface $image) {}

            public function getImage(?int $width = null, ?int $height = null): ?ImageInterface
            {
                return $this->image;
            }
        };

        $decorator = new PostObjectWithImageVisibility($post, true);

        $this->assertSame($image, $decorator->getImage());
    }

    #[TestDox('returns no featured image when image display is disabled')]
    public function testReturnsNoImageWhenDisplayIsDisabled(): void
    {
        $image = $this->createMock(ImageInterface::class);
        $post = new class ($image) extends NullPostObject {
            public function __construct(private ImageInterface $image) {}

            public function getImage(?int $width = null, ?int $height = null): ?ImageInterface
            {
                return $this->image;
            }
        };

        $decorator = new PostObjectWithImageVisibility($post, false);

        $this->assertNull($decorator->getImage());
    }
}
