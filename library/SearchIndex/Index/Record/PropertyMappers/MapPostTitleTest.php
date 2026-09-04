<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Index\Record\PropertyMappers;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

/**
 * Tests mapping normalized post titles.
 */
class MapPostTitleTest extends TestCase
{
    #[TestDox('Applies title filters and normalizes text')]
    public function testMapProperty(): void
    {
        $post = new \WP_Post([]);
        $post->post_title = 'Original title';
        $wpService = new FakeWpService([
            'applyFilters' => '<script>ignored()</script><strong>Filtered</strong>  title',
        ]);

        $result = (new MapPostTitle($wpService))->mapProperty($post);

        static::assertSame('Filtered title', $result);
    }
}