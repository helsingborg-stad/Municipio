<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Index\Record\PropertyMappers;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

/**
 * Tests mapping the modification timestamp.
 */
class MapPostModifiedTest extends TestCase
{
    #[TestDox('Maps the modification timestamp')]
    public function testMapProperty(): void
    {
        $post = new \WP_Post([]);
        $post->post_modified = '2026-08-02 11:45:00';

        $result = (new MapPostModified())->mapProperty($post);

        static::assertSame(strtotime('2026-08-02 11:45:00'), $result);
    }
}