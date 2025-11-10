<?php

namespace Municipio\PostsList\Config\GetPostsConfig;

use Municipio\PostsList\GetPosts\GetPostsFromPostsListConfig;
use PHPUnit\Framework\Attributes\TestDox;
use WpService\Contracts\GetPosts;

class GetPostsFromPostsListConfigTest extends \PHPUnit\Framework\TestCase
{
    #[TestDox('returns array')]
    public function testGetPostsReturnsArray(): void
    {
        $wpService = new class implements GetPosts {
            public function getPosts(?array $args = null): array
            {
                return [];
            }
        };

        $getPosts = new GetPostsFromPostsListConfig(new DefaultGetPostsConfig(), $wpService);

        $this->assertIsArray($getPosts->getPosts());
    }
}
