<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Index\Record\PropertyMappers;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

/**
 * Tests mapping the publication timestamp.
 */
class MapPostDateTest extends TestCase
{
    #[TestDox('Maps the publication timestamp')]
    public function testMapProperty(): void
    {
        $post = new \WP_Post([]);
        $post->post_date = '2026-08-01 10:30:00';

        $result = (new MapPostDate())->mapProperty($post);

        static::assertSame(strtotime('2026-08-01 10:30:00'), $result);
    }
}