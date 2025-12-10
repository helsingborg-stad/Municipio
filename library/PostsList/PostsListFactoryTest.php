<?php

namespace Municipio\PostsList;

use Municipio\PostsList\Config\AppearanceConfig\AppearanceConfigInterface;
use Municipio\PostsList\Config\FilterConfig\FilterConfigInterface;
use Municipio\PostsList\Config\GetPostsConfig\GetPostsConfigInterface;
use Municipio\PostsList\ConfigMapper\PostsListConfigDTOInterface;
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
        $postsList = $factory->create($this->createMock(PostsListConfigDTOInterface::class));

        $this->assertInstanceOf(PostsList::class, $postsList);
    }
}
