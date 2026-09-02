<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Index\Record\PropertyMappers;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

/**
 * Tests mapping the WordPress post ID.
 */
class MapPostIdTest extends TestCase
{
    #[TestDox('Maps the post ID as a string')]
    public function testMapProperty(): void
    {
        $post = new \WP_Post([]);
        $post->ID = 42;

        $result = (new MapPostId())->mapProperty($post);

        static::assertSame('42', $result);
    }
}