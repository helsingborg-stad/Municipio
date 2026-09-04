<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Index\Record\PropertyMappers;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

/**
 * Tests mapping normalized post excerpts.
 */
class MapExcerptTest extends TestCase
{
    #[TestDox('Removes shortcodes and normalizes excerpt text')]
    public function testMapProperty(): void
    {
        $post = new \WP_Post([]);
        $post->post_content = 'Fallback content';
        $wpService = new FakeWpService([
            'getTheExcerpt' => '[gallery]<p>Useful   excerpt</p>',
            'wpTrimWords' => static fn(string $text): string => $text,
        ]);

        $result = (new MapExcerpt($wpService))->mapProperty($post);

        static::assertSame('Useful excerpt', $result);
    }
}