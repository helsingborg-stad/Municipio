<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Index\Record\PropertyMappers;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

/**
 * Tests mapping non-category taxonomy terms.
 */
class MapTagsTest extends TestCase
{
    #[TestDox('Maps non-category terms and ignores taxonomy errors')]
    public function testMapProperty(): void
    {
        $post = new \WP_Post([]);
        $post->ID = 42;
        $topic = new \WP_Term([]);
        $topic->name = 'Environment';
        $wpService = new FakeWpService([
            'getPostTaxonomies' => ['category', 'topic', 'broken'],
            'wpGetPostTerms' => static fn(int $postId, string $taxonomy): array|\WP_Error => match ($taxonomy) {
                'topic' => [$topic],
                default => new \WP_Error('invalid_taxonomy'),
            },
        ]);

        $result = (new MapTags($wpService))->mapProperty($post);

        static::assertSame(['Environment'], $result);
    }
}