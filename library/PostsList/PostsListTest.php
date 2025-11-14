<?php

namespace Municipio\PostsList;

use Municipio\PostsList\Config\AppearanceConfig\DefaultAppearanceConfig;
use Municipio\PostsList\Config\FilterConfig\DefaultFilterConfig;
use Municipio\PostsList\Config\GetPostsConfig\DefaultGetPostsConfig;
use Municipio\PostsList\GetPosts\WpQueryFactoryInterface;
use Municipio\PostsList\QueryVarRegistrar\QueryVarRegistrar;
use Municipio\PostsList\QueryVars\QueryVars;
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
        $wpQueryFactory   = $this->getWpQueryFactory();
        $wpService        = new FakeWpService(['getPosts' => [], 'addFilter' => true]);
        $queryVars        = new QueryVars('posts_list_');
        $postsList        = new PostsList($getPostsConfig, $appearanceConfig, $filterConfig, [], $wpQueryFactory, $queryVars, $wpService);

        $this->assertIsArray($postsList->getData());
    }

    private function getWpQueryFactory(): WpQueryFactoryInterface
    {
        return new class implements \Municipio\PostsList\GetPosts\WpQueryFactoryInterface {
            public static function create($query = ''): \WP_Query
            {
                return new class extends \WP_Query {
                    public function get_posts() // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
                    {
                        $this->posts         = [];
                        $this->max_num_pages = 0;
                        return [];
                    }
                };
            }
        };
    }
}
