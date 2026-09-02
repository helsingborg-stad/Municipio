<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Index\Record\PropertyMappers;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

/**
 * Tests mapping post thumbnail URLs.
 */
class MapThumbnailTest extends TestCase
{
    #[TestDox('Maps an image URL and handles a missing thumbnail')]
    public function testMapProperty(): void
    {
        $post = new \WP_Post([]);
        $post->ID = 42;

        $thumbnail = (new MapThumbnail(new FakeWpService([
            'getPostThumbnailId' => 99,
            'getThePostThumbnailUrl' => 'https://example.test/thumbnail.jpg',
        ])))->mapProperty($post);
        $missingThumbnail = (new MapThumbnail(new FakeWpService([
            'getPostThumbnailId' => false,
        ])))->mapProperty($post);

        static::assertSame('https://example.test/thumbnail.jpg', $thumbnail);
        static::assertSame('', $missingThumbnail);
    }
}