<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Index\Record\PropertyMappers;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

/**
 * Tests mapping the current multisite blog ID.
 */
class MapBlogIdTest extends TestCase
{
    #[TestDox('Maps the current blog ID')]
    public function testMapProperty(): void
    {
        $post = new \WP_Post([]);
        $wpService = new FakeWpService(['getCurrentBlogId' => 7]);

        $result = (new MapBlogId($wpService))->mapProperty($post);

        static::assertSame(7, $result);
    }
}