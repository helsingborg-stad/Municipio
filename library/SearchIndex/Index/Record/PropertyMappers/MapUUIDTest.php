<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Index\Record\PropertyMappers;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

/**
 * Tests mapping the stable search record ID.
 */
class MapUUIDTest extends TestCase
{
    #[TestDox('Includes the normalized host, blog ID, and post ID')]
    public function testMapProperty(): void
    {
        $post = new \WP_Post([]);
        $post->ID = 42;
        $wpService = new FakeWpService([
            'homeUrl' => 'https://www.example.test/path',
            'isMultisite' => true,
            'getCurrentBlogId' => 7,
        ]);

        $result = (new MapUUID($wpService))->mapProperty($post);

        static::assertSame('www-example-test-7-42', $result);
    }
}