<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Index\Record\PropertyMappers;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

/**
 * Tests mapping the formatted publication date.
 */
class MapPostDateFormattedTest extends TestCase
{
    #[TestDox('Uses the configured date format')]
    public function testMapProperty(): void
    {
        $post = new \WP_Post([]);
        $post->post_date = '2026-08-01 10:30:00';
        $wpService = new FakeWpService(['getOption' => 'Y-m-d']);

        $result = (new MapPostDateFormatted($wpService))->mapProperty($post);

        static::assertSame('2026-08-01', $result);
    }
}