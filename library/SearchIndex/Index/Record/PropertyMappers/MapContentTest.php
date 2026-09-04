<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Index\Record\PropertyMappers;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

/**
 * Tests mapping normalized post content.
 */
class MapContentTest extends TestCase
{
    #[TestDox('Applies content filters and normalizes text')]
    public function testMapProperty(): void
    {
        $post = new \WP_Post([]);
        $post->ID = 42;
        $post->post_content = 'Original content';
        $wpService = new FakeWpService([
            'applyFilters' => static fn(string $hookName, mixed $value): mixed => match ($hookName) {
                'the_content' => '<style>ignored</style><p>Filtered   content</p>',
                'Municipio/SearchIndex/Record/Content' => $value . ' final',
                default => $value,
            },
        ]);

        $result = (new MapContent($wpService))->mapProperty($post);

        static::assertSame('Filtered content final', $result);
    }

    #[TestDox('strips style tags from content along with their contents')]
    public function testStripsStyleTags(): void {
        $post = new \WP_Post([]);
        $post->ID = 42;
        $post->post_content = '<style>ignored</style><p>Content</p>';
        $wpService = new FakeWpService([
            'applyFilters' => static fn(string $hookName, mixed $value): mixed => match ($hookName) {
                'the_content' => $value,
                'Municipio/SearchIndex/Record/Content' => $value,
                default => $value,
            },
        ]);

        $result = (new MapContent($wpService))->mapProperty($post);

        static::assertSame('Content', $result);
    }

    #[TestDox('strips script tags from content along with their contents')]
    public function testStripsScriptTags(): void {
        $post = new \WP_Post([]);
        $post->ID = 42;
        $post->post_content = '<script>ignored</script><p>Content</p>';
        $wpService = new FakeWpService([
            'applyFilters' => static fn(string $hookName, mixed $value): mixed => match ($hookName) {
                'the_content' => $value,
                'Municipio/SearchIndex/Record/Content' => $value,
                default => $value,
            },
        ]);

        $result = (new MapContent($wpService))->mapProperty($post);

        static::assertSame('Content', $result);
    }
}