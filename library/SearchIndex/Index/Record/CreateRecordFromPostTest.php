<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Index\Record;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;
use WpService\WpService;

/**
 * Tests complete search record creation from WordPress posts.
 */
class CreateRecordFromPostTest extends TestCase
{
    #[TestDox('createRecordFromPost() can create a record with the required WordPress services')]
    public function testCreateRecordFromPostCanCreateRecord(): void
    {
        $factory = new CreateRecordFromPost(static::createWpService());
        $record = $factory->createRecordFromPost(static::createPost(['ID' => 42, 'post_title' => 'Example title']));
        static::assertIsArray($record);
    }

    private static function createWpService(): WpService {
        return new FakeWpService([
            'homeUrl' => 'https://www.example.test/path',
            'isMultisite' => true,
            'getCurrentBlogId' => 7,
            'applyFilters' => static fn(string $hookName, mixed $value) => $value,
            'getTheExcerpt' => '[shortcode]<p>Useful excerpt</p>',
            'wpTrimWords' => static fn(string $text): string => $text,
            'getPostPermalink' => 'https://www.example.test/example-title',
            'getOption' => 'Y-m-d',
            'getPostThumbnailId' => 99,
            'getThePostThumbnailUrl' => 'https://www.example.test/thumbnail.jpg',
            'getPostMeta' => 'An accessible description',
            'getPostTaxonomies' => ['category', 'topic'],
            'wpGetPostTerms' => static fn() => [],
            'currentTime' => '2026-09-01 09:15:00',
            'getPostType' => 'page',
            'getPostTypeObject' => new \WP_Post_Type('page'),
            'getPostAncestors' => [10, 5],
            'getPost' => static::createPost(['ID' => 10, 'post_title' => 'Parent post']),
            'getBloginfo' => static fn() => ''
        ]);
    }

    private static function createPost(array $properties): \WP_Post
    {
        $post = new \WP_Post([]);

        foreach ($properties as $property => $value) {
            $post->{$property} = $value;
        }

        return $post;
    }
}