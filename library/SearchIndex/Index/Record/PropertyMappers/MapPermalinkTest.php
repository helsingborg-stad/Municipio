<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Index\Record\PropertyMappers;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

/**
 * Tests mapping post permalinks.
 */
class MapPermalinkTest extends TestCase
{
    #[TestDox('Maps a URL and handles a missing permalink')]
    public function testMapProperty(): void
    {
        $post = static::createPost(42, 'page');

        $permalink = (new MapPermalink(new FakeWpService([
            'getPermalink' => 'https://example.test/example',
        ])))->mapProperty($post);
        $missingPermalink = (new MapPermalink(new FakeWpService([
            'getPermalink' => false,
        ])))->mapProperty($post);

        static::assertSame('https://example.test/example', $permalink);
        static::assertSame('', $missingPermalink);
    }

    #[TestDox('Maps the permalink using get_permalink() with the post as argument')]
    public function testUsesGetPermalinkWithThePost(): void
    {
        $post      = static::createPost(159, 'page');
        $wpService = new FakeWpService(['getPermalink' => 'https://example.test/hr/example/']);

        (new MapPermalink($wpService))->mapProperty($post);

        static::assertArrayHasKey('getPermalink', $wpService->methodCalls);
        static::assertSame($post, $wpService->methodCalls['getPermalink'][0][0]);
    }

    /**
     * get_post_permalink() returns a plain "?post_type=page&p=159" URL for pages,
     * regardless of the configured permalink structure, so get_permalink() must be used.
     */
    #[TestDox('Maps pretty permalinks for pages instead of plain post_type/p query URLs')]
    public function testMapsPrettyPermalinkForPages(): void
    {
        $post      = static::createPost(159, 'page');
        $wpService = new FakeWpService([
            'getPermalink'     => 'https://intranat.example.test/hr/lon-ersattningar-och-formaner/sa-satts-din-lon/',
            'getPostPermalink' => 'https://intranat.example.test/hr/?post_type=page&p=159',
        ]);

        $permalink = (new MapPermalink($wpService))->mapProperty($post);

        static::assertSame(
            'https://intranat.example.test/hr/lon-ersattningar-och-formaner/sa-satts-din-lon/',
            $permalink
        );
        static::assertStringNotContainsString('post_type=', $permalink);
        static::assertStringNotContainsString('p=159', $permalink);
    }

    /**
     * Sites configured with the plain permalink structure should still be indexed
     * with the URL WordPress resolves, without any rewriting by the mapper.
     */
    #[TestDox('Maps the plain permalink as-is when WordPress is configured with a plain structure')]
    public function testMapsPlainPermalinkWhenConfigured(): void
    {
        $post      = static::createPost(159, 'page');
        $wpService = new FakeWpService(['getPermalink' => 'https://intranat.example.test/hr/?page_id=159']);

        $permalink = (new MapPermalink($wpService))->mapProperty($post);

        static::assertSame('https://intranat.example.test/hr/?page_id=159', $permalink);
    }

    private static function createPost(int $id, string $postType): \WP_Post
    {
        $post            = new \WP_Post([]);
        $post->ID        = $id;
        $post->post_type = $postType;

        return $post;
    }
}