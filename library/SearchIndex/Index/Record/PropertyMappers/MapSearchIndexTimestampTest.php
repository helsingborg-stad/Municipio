<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Index\Record\PropertyMappers;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

/**
 * Tests mapping the search indexing timestamp.
 */
class MapSearchIndexTimestampTest extends TestCase
{
    #[TestDox('Maps the current site-local time')]
    public function testMapProperty(): void
    {
        $post = new \WP_Post([]);
        $wpService = new FakeWpService(['currentTime' => '2026-09-01 09:15:00']);

        $result = (new MapSearchIndexTimestamp($wpService))->mapProperty($post);

        static::assertSame('2026-09-01 09:15:00', $result);
    }
}