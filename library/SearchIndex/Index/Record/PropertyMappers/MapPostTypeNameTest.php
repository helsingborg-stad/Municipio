<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Index\Record\PropertyMappers;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

/**
 * Tests mapping post type display names.
 */
class MapPostTypeNameTest extends TestCase
{
    #[TestDox('Maps the plural post type label')]
    public function testMapProperty(): void
    {
        $post = new \WP_Post([]);
        $postType = new \WP_Post_Type('page');
        $postType->labels = (object) ['name' => 'Pages'];
        $wpService = new FakeWpService([
            'getPostType' => 'page',
            'getPostTypeObject' => $postType,
        ]);

        $result = (new MapPostTypeName($wpService))->mapProperty($post);

        static::assertSame('Pages', $result);
    }
}