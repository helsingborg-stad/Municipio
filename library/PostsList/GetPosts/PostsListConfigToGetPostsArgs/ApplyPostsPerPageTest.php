<?php

namespace Municipio\PostsList\GetPosts\PostsListConfigToGetPostsArgs;

use Municipio\PostsList\Config\GetPostsConfig\DefaultGetPostsConfig;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class ApplyPostsPerPageTest extends TestCase
{
    #[TestDox('Applies posts per page from config to args')]
    public function testApplyAddsPostsPerPageToArgs(): void
    {
        $config = new class extends DefaultGetPostsConfig {
            public function getPostsPerPage(): int
            {
                return 25;
            }
        };

        $applier = new ApplyPostsPerPage();
        $args    = $applier->apply($config, []);

        $this->assertEquals(25, $args['posts_per_page']);
    }
}
