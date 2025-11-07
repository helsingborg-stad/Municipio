<?php

namespace Municipio\PostsList\Config\GetPostsConfig;

use Municipio\PostsList\GetPosts\GetPostsFromPostsListConfig;
use PHPUnit\Framework\Attributes\TestDox;
use WpService\Contracts\GetPosts;

class GetPostsFromPostsListConfigTest extends \PHPUnit\Framework\TestCase
{
    #[TestDox('uses post types from config')]
    public function testGetPostsReturnsArray(): void
    {
        $assert    = fn($postTypes) => $this->assertEquals(['test_post_type'], $postTypes);
        $config    = new class extends DefaultGetPostsConfig {
            public function getPostTypes(): array
            {
                return ['test_post_type'];
            }
        };
        $wpService = new class ($assert) implements GetPosts {
            public function __construct(private $assert)
            {
            }
            public function getPosts(?array $args = null): array
            {
                ($this->assert)($args['post_type']);
                return [];
            }
        };

        $getPosts = new GetPostsFromPostsListConfig($config, $wpService);
        $getPosts->getPosts();
    }

    #[TestDox('uses posts per page from config')]
    public function testGetPostsUsesPostsPerPageFromConfig(): void
    {
        $assert    = fn($postsPerPage) => $this->assertEquals(5, $postsPerPage);
        $config    = new class extends DefaultGetPostsConfig {
            public function getPostsPerPage(): int
            {
                return 5;
            }
        };
        $wpService = new class ($assert) implements GetPosts {
            public function __construct(private $assert)
            {
            }
            public function getPosts(?array $args = null): array
            {
                ($this->assert)($args['posts_per_page']);
                return [];
            }
        };

        $getPosts = new GetPostsFromPostsListConfig($config, $wpService);
        $getPosts->getPosts();
    }

    #[TestDox('applies search from config')]
    public function testGetPostsAppliesSearchFromConfig(): void
    {
        $assert    = fn($search) => $this->assertEquals('test search', $search);
        $config    = new class extends DefaultGetPostsConfig {
            public function getSearch(): ?string
            {
                return 'test search';
            }
        };
        $wpService = new class ($assert) implements GetPosts {
            public function __construct(private $assert)
            {
            }
            public function getPosts(?array $args = null): array
            {
                ($this->assert)($args['s']);
                return [];
            }
        };

        $getPosts = new GetPostsFromPostsListConfig($config, $wpService);
        $getPosts->getPosts();
    }
}
