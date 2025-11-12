<?php

namespace Municipio\PostsList\Config\GetPostsConfig;

use Municipio\PostsList\GetPosts\MapPostArgsFromPostsListConfig;
use PHPUnit\Framework\Attributes\TestDox;
use WpService\Contracts\GetPosts;

class MapPostArgsFromPostsListConfigTest extends \PHPUnit\Framework\TestCase
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

        $getPosts = new MapPostArgsFromPostsListConfig(new DefaultGetPostsConfig(), $wpService);

        $this->assertIsArray($getPosts->getPostsArgs());
    }
}
