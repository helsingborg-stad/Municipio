<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Index\Record\PropertyMappers;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

/**
 * Tests mapping post thumbnail alternative text.
 */
class MapThumbnailAltTest extends TestCase
{
    #[TestDox('Maps alternative text and handles a missing thumbnail')]
    public function testMapProperty(): void
    {
        $post = new \WP_Post([]);
        $post->ID = 42;

        $alternativeText = (new MapThumbnailAlt(new FakeWpService([
            'getPostThumbnailId' => 99,
            'getPostMeta' => 'An accessible description',
        ])))->mapProperty($post);
        $missingAlternativeText = (new MapThumbnailAlt(new FakeWpService([
            'getPostThumbnailId' => false,
        ])))->mapProperty($post);

        static::assertSame('An accessible description', $alternativeText);
        static::assertSame('', $missingAlternativeText);
    }
}