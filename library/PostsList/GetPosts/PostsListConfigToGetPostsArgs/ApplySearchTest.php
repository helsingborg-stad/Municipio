<?php

namespace Municipio\PostsList\GetPosts\PostsListConfigToGetPostsArgs;

use Municipio\PostsList\Config\GetPostsConfig\DefaultGetPostsConfig;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class ApplySearchTest extends TestCase
{
    #[TestDox('Applies search from config to args')]
    public function testApplyAddsSearchToArgs(): void
    {
        $config = new class extends DefaultGetPostsConfig {
            public function getSearch(): string
            {
                return 'test search';
            }
        };

        $applier = new ApplySearch();
        $args    = $applier->apply($config, []);

        $this->assertArrayHasKey('s', $args);
        $this->assertEquals('test search', $args['s']);
    }
}
