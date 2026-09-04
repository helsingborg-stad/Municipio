<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Index\Record\PropertyMappers;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

/**
 * Tests mapping post permalinks.
 */
class MapPermalinkTest extends TestCase
{
    #[TestDox('Maps a URL and handles a missing permalink')]
    public function testMapProperty(): void
    {
        $post = new \WP_Post([]);
        $post->ID = 42;

        $permalink = (new MapPermalink(new FakeWpService([
            'getPostPermalink' => 'https://example.test/example',
        ])))->mapProperty($post);
        $missingPermalink = (new MapPermalink(new FakeWpService([
            'getPostPermalink' => false,
        ])))->mapProperty($post);

        static::assertSame('https://example.test/example', $permalink);
        static::assertSame('', $missingPermalink);
    }
}