<?php

namespace Municipio\PostsList;

use Municipio\PostsList\Config\AppearanceConfig\DefaultAppearanceConfig;
use Municipio\PostsList\Config\FilterConfig\DefaultFilterConfig;
use Municipio\PostsList\Config\GetPostsConfig\DefaultGetPostsConfig;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class PostsListTest extends TestCase
{
    #[TestDox('getData returns an array')]
    public function testGetDataReturnsArray(): void
    {
        $getPostsConfig   = new  DefaultGetPostsConfig();
        $appearanceConfig = new DefaultAppearanceConfig();
        $filterConfig     = new DefaultFilterConfig();
        $wpService        = new FakeWpService(['getPosts' => []]);
        $postsList        = new PostsList($getPostsConfig, $appearanceConfig, $filterConfig, $wpService);

        $this->assertIsArray($postsList->getData());
    }
}
