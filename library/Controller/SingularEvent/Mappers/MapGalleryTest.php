<?php

namespace Municipio\Controller\SingularEvent\Mappers;

use ComponentLibrary\Integrations\Image\ImageResolverInterface;
use Municipio\Schema\Schema;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class MapGalleryTest extends TestCase
{
    #[TestDox('maps images to gallery component attributes')]
    public function testMapsImages(): void
    {
        $event = Schema::event()->image([
            Schema::imageObject()->setProperty('@id', 1)->description('First'),
            Schema::imageObject()->setProperty('@id', 2),
        ]);

        $result = (new MapGallery($this->getImageResolver()))->map($event);

        $this->assertCount(2, $result['list']);
        $this->assertSame('url-1-768', $result['list'][0]['largeImage']);
        $this->assertSame('url-1-450', $result['list'][0]['smallImage']);
        $this->assertSame('First', $result['list'][0]['alt']);
        $this->assertSame('', $result['list'][1]['alt']);
        $this->assertNull($result['list'][0]['caption']);
    }

    #[TestDox('leaves out the featured image')]
    public function testLeavesOutFeaturedImage(): void
    {
        $event = Schema::event()->image([
            Schema::imageObject()->setProperty('@id', 1),
            Schema::imageObject()->setProperty('@id', 2),
        ]);

        $result = (new MapGallery($this->getImageResolver(), 1))->map($event);

        $this->assertCount(1, $result['list']);
        $this->assertSame('url-2-768', $result['list'][0]['largeImage']);
    }

    #[TestDox('returns null when the only image is the featured image')]
    public function testReturnsNullWhenOnlyFeaturedImage(): void
    {
        $event = Schema::event()->image([Schema::imageObject()->setProperty('@id', 1)]);

        $this->assertNull((new MapGallery($this->getImageResolver(), 1))->map($event));
    }

    #[TestDox('handles a single image object that is not wrapped in an array')]
    public function testHandlesSingleImageObject(): void
    {
        $event = Schema::event()->image(Schema::imageObject()->setProperty('@id', 3));

        $result = (new MapGallery($this->getImageResolver()))->map($event);

        $this->assertSame('url-3-768', $result['list'][0]['largeImage']);
    }

    #[TestDox('returns null when there are no usable images')]
    public function testReturnsNullWithoutImages(): void
    {
        $mapper = new MapGallery($this->getImageResolver());

        $this->assertNull($mapper->map(Schema::event()));
        $this->assertNull($mapper->map(Schema::event()->image([])));
        $this->assertNull($mapper->map(Schema::event()->image(['https://example.com/image.jpg'])));
        $this->assertNull($mapper->map(Schema::event()->image([Schema::imageObject()])));
    }

    #[TestDox('leaves out images whose url can not be resolved')]
    public function testLeavesOutUnresolvableImages(): void
    {
        $event = Schema::event()->image([Schema::imageObject()->setProperty('@id', 404)]);

        $this->assertNull((new MapGallery($this->getImageResolver()))->map($event));
    }

    #[TestDox('works with exhibition events')]
    public function testWorksWithExhibitionEvent(): void
    {
        $event = Schema::exhibitionEvent()->image([Schema::imageObject()->setProperty('@id', 5)]);

        $result = (new MapGallery($this->getImageResolver()))->map($event);

        $this->assertSame('url-5-768', $result['list'][0]['largeImage']);
    }

    private function getImageResolver(): ImageResolverInterface
    {
        return new class implements ImageResolverInterface {
            public function getImageUrl(int $id, array $size): ?string
            {
                return $id === 404 ? null : "url-{$id}-{$size[0]}";
            }

            public function getImageAltText(int $id): ?string
            {
                return null;
            }
        };
    }
}
