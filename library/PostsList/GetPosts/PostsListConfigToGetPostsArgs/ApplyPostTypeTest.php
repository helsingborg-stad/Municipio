<?php

namespace Municipio\PostsList\GetPosts\PostsListConfigToGetPostsArgs;

use Municipio\PostsList\Config\DefaultPostsListConfig;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class ApplyPostTypeTest extends TestCase
{
    #[TestDox('maps post types from config to args array')]
    public function testMapPostTypes(): void
    {
        $config = new class extends DefaultPostsListConfig {
            public function getPostTypes(): array
            {
                return ['test_post_type'];
            }
        };

        $mapper = new ApplyPostType();

        $this->assertSame(['post_type' => ['test_post_type']], $mapper->apply($config, []));
    }
}
