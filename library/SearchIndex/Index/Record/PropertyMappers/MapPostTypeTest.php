<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Index\Record\PropertyMappers;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

/**
 * Tests mapping post type slugs.
 */
class MapPostTypeTest extends TestCase
{
    #[TestDox('Maps the post type and handles an unavailable type')]
    public function testMapProperty(): void
    {
        $post = new \WP_Post([]);

        $postType = (new MapPostType(new FakeWpService(['getPostType' => 'page'])))
            ->mapProperty($post);
        $missingPostType = (new MapPostType(new FakeWpService(['getPostType' => false])))
            ->mapProperty($post);

        static::assertSame('page', $postType);
        static::assertSame('', $missingPostType);
    }
}