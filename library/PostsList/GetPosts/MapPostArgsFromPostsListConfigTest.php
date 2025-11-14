<?php

namespace Municipio\PostsList\Config\GetPostsConfig;

use Municipio\PostsList\Config\FilterConfig\DefaultFilterConfig;
use Municipio\PostsList\GetPosts\MapPostArgsFromPostsListConfig;
use PHPUnit\Framework\Attributes\TestDox;

class MapPostArgsFromPostsListConfigTest extends \PHPUnit\Framework\TestCase
{
    #[TestDox('returns array')]
    public function testGetPostsReturnsArray(): void
    {
        $getPosts = new MapPostArgsFromPostsListConfig(new DefaultGetPostsConfig(), new DefaultFilterConfig());

        $this->assertIsArray($getPosts->getPostsArgs());
    }
}
