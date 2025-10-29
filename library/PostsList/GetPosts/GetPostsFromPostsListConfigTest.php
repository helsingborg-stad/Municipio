<?php

namespace Municipio\PostsList\GetPosts;

use Municipio\PostsList\Config\DefaultPostsListConfig;
use PHPUnit\Framework\Attributes\TestDox;
use WpService\Contracts\GetPosts;

class GetPostsFromPostsListConfigTest extends \PHPUnit\Framework\TestCase
{
    #[TestDox('uses post types from config')]
    public function testGetPostsReturnsArray(): void
    {
        $assert    = fn($postTypes) => $this->assertEquals(['test_post_type'], $postTypes);
        $config    = new class extends DefaultPostsListConfig {
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
}
