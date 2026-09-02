<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Index\Record\PropertyMappers;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

/**
 * Tests mapping configured site information.
 */
class MapBlogInfoTest extends TestCase
{
    #[TestDox('Maps the configured site property')]
    public function testMapProperty(): void
    {
        $post = new \WP_Post([]);
        $wpService = new FakeWpService([
            'getBloginfo' => static fn(string $show): string => match ($show) {
                'name' => 'Example municipality',
                'url' => 'https://example.test',
            },
        ]);

        $name = (new MapBlogInfo($wpService, 'name'))->mapProperty($post);
        $url = (new MapBlogInfo($wpService, 'url'))->mapProperty($post);

        static::assertSame('Example municipality', $name);
        static::assertSame('https://example.test', $url);
    }
}