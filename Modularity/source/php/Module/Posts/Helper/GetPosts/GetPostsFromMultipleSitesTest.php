<?php

namespace Modularity\Module\Posts\Helper\GetPosts;

use Modularity\Module\Posts\Helper\GetPosts\PostTypesFromSchemaType\NullPostTypesFromSchemaTypeResolver;
use Modularity\Module\Posts\Helper\GetPosts\PostTypesFromSchemaType\PostTypesFromSchemaTypeResolverInterface;
use Modularity\Module\Posts\Helper\GetPosts\UserGroupResolver\UserGroupResolverInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Spatie\Snapshots\MatchesSnapshots;
use WP_Post;
use WP_Site;
use WpService\Contracts\{
    EscSql,
    GetBlogDetails,
    GetBlogPost,
    GetOption,
    IsUserLoggedIn,
    RestoreCurrentBlog,
    SwitchToBlog,
};

class GetPostsFromMultipleSitesTest extends TestCase
{
    use MatchesSnapshots;

    #[TestDox('class can be instantiated')]
    public function testCanBeInstantiated(): void
    {
        $instance = $this->createInstance();
        $this->assertInstanceOf(GetPostsFromMultipleSites::class, $instance);
    }

    #[TestDox('if no sites are provided, an empty result is returned')]
    public function testReturnsEmptyResultIfNoSitesProvided(): void
    {
        $currentSite = $this->createSite(1);
        $wpService = $this->getWpServiceMock();
        $wpService->method('getBlogDetails')->willReturn($currentSite);

        $instance = $this->createInstance([], 1, [], $this->getWpdbMock(), $wpService);

        $result = $instance->getPosts();

        $this->assertEquals([], $result->getPosts());
        $this->assertEquals(0, $result->getNumberOfPages());
        $this->assertEquals([], $result->getStickyPosts());
    }

    #[TestDox('returns result with posts from multiple sites and correct sql query')]
    #[DataProvider('fieldsProvider')]
    public function testReturnsPostsFromMultipleSites($fields): void
    {
        $fields = array_merge([
            'posts_data_source' => 'posttype',
            'posts_data_post_type' => 'custom_post_type',
        ], $fields);

        $postFromBlog1 = $this->createPost(1, 'Post from Blog 1', '2023-01-01');
        $postFromBlog2 = $this->createPost(2, 'Post from Blog 2', '2023-01-02');

        $wpdb = $this->getWpdbMock();
        $wpdb->method('get_results')->willReturn([
            (object)[
                'blog_id' => 1,
                'post_id' => $postFromBlog1->ID,
                'post_title' => $postFromBlog1->post_title,
                'post_date' => $postFromBlog1->post_date,
                'post_status' => $postFromBlog1->post_status,
                'is_sticky' => "0",
            ],
            (object)[
                'blog_id' => 2,
                'post_id' => $postFromBlog2->ID,
                'post_title' => $postFromBlog2->post_title,
                'post_date' => $postFromBlog2->post_date,
                'post_status' => $postFromBlog2->post_status,
                'is_sticky' => "1",
            ],
        ]);

        $currentSite = $this->createSite(1);
        $wpService = $this->getWpServiceMock();
        $wpService->method('getBlogDetails')->willReturn($currentSite);
        $wpService->method('getBlogPost')->willReturnOnConsecutiveCalls($postFromBlog1, $postFromBlog2);

        $instance = $this->createInstance($fields, 1, [1, 2], $wpdb, $wpService);

        $result = $instance->getPosts();

        $this->assertCount(1, $result->getPosts());
        $this->assertCount(1, $result->getStickyPosts());
        $this->assertEquals(1, $result->getNumberOfPages());
        $this->assertMatchesTextSnapshot($instance->getSql());
    }

    public static function fieldsProvider(): array
    {
        return [
            'sort by date' => [['posts_sort_by' => 'date']],
            'sort by title' => [['posts_sort_by' => 'title']],
            'sort by modified' => [['posts_sort_by' => 'modified']],
            'sort by menu order' => [['posts_sort_by' => 'menu_order']],
            'sort by random' => [['posts_sort_by' => 'rand']],
            'post type from schema' => [['posts_data_source' => 'schematype', 'posts_data_schema_type' => 'JobPosting']],
            'custom count' => [['posts_count' => 5]],
        ];
    }

    private function createInstance(
        array $fields = [],
        int $siteId = 1,
        array $sites = [],
        $wpdb = null,
        $wpService = null,
        $resolver = null
    ): GetPostsFromMultipleSites {
        return new GetPostsFromMultipleSites(
            $fields,
            $siteId,
            $sites,
            $wpdb ?? $this->getWpdbMock(),
            $wpService ?? $this->getWpServiceMock(),
            $resolver ?? $this->getPostsTypesFromSchemaTypeResolverMock(),
            $this->getUserGroupResolverMock()
        );
    }

    private function createSite(int $blogId): WP_Site
    {
        $site = new WP_Site([]);
        $site->blog_id = $blogId;
        return $site;
    }

    private function createPost(int $id, string $title, string $date): WP_Post
    {
        $post = new WP_Post([]);
        $post->ID = $id;
        $post->post_title = $title;
        $post->post_date = $date;
        $post->post_status = 'publish';
        return $post;
    }

    private function getWpdbMock(): \wpdb|MockObject
    {
        if (!defined('OBJECT')) {
            define('OBJECT', 'object');
        }
        $wpdb = $this->createMock(\wpdb::class);
        $wpdb->base_prefix = 'mun_';
        return $wpdb;
    }

    private function getPostsTypesFromSchemaTypeResolverMock(): PostTypesFromSchemaTypeResolverInterface|MockObject
    {
        return $this->createMock(NullPostTypesFromSchemaTypeResolver::class);
    }

    private function getWpServiceMock(): IsUserLoggedIn|EscSql|GetBlogDetails|SwitchToBlog|RestoreCurrentBlog|GetBlogPost|GetOption|MockObject
    {
        $wpService = $this->createMockForIntersectionOfInterfaces([
            IsUserLoggedIn::class,
            EscSql::class,
            GetBlogDetails::class,
            SwitchToBlog::class,
            RestoreCurrentBlog::class,
            GetBlogPost::class,
            GetOption::class
        ]);
        $wpService->method('escSql')->willReturnArgument(0);
        $wpService->method('getOption')->willReturn([2]);
        return $wpService;
    }

    private function getUserGroupResolverMock(): UserGroupResolverInterface|MockObject
    {
        return $this->createMock(UserGroupResolverInterface::class);
    }
}