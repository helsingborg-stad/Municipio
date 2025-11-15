<?php

namespace Municipio\PostsList\GetPosts\PostsListConfigToGetPostsArgs;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class ApplyPageTest extends TestCase
{
    #[TestDox('apply adds correct paged argument to args array')]
    public function testApply(): void
    {
        $config = new class extends \Municipio\PostsList\Config\GetPostsConfig\DefaultGetPostsConfig {
            public function getPage(): int
            {
                return 3;
            }
        };

        $applier = new ApplyPage();
        $args    = $applier->apply($config, []);

        $this->assertEquals(3, $args['paged']);
    }

    #[TestDox('apply prevents negative page numbers')]
    public function testApplyPreventsNegativePageNumbers(): void
    {
        $config = new class extends \Municipio\PostsList\Config\GetPostsConfig\DefaultGetPostsConfig {
            public function getPage(): int
            {
                return -22;
            }
        };

        $applier = new ApplyPage();
        $args    = $applier->apply($config, []);

        $this->assertEquals(1, $args['paged']);
    }
}
