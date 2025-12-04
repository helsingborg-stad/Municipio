<?php

namespace Municipio\PostsList;

use Municipio\PostsList\Config\AppearanceConfig\AppearanceConfigInterface;
use Municipio\PostsList\Config\FilterConfig\FilterConfigInterface;
use Municipio\PostsList\Config\GetPostsConfig\GetPostsConfigInterface;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class PostsListFactoryTest extends TestCase
{
    #[TestDox('creates PostsList instance')]
    public function testCreatePostsListInstance(): void
    {
        $wpService = new FakeWpService(['addFilter' => true]);
        $wpdb = new class('', '', '', '') extends \wpdb {};
        $factory = new PostsListFactory($wpService, $wpdb);
        $postsList = $factory->create(
            $this->createMock(GetPostsConfigInterface::class),
            $this->createMock(AppearanceConfigInterface::class),
            $this->createMock(FilterConfigInterface::class),
            'test_prefix_',
        );

        $this->assertInstanceOf(PostsList::class, $postsList);
    }
}
