<?php

namespace Municipio\PostsList\Config\GetPostsConfig;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class DefaultGetPostsConfigTest extends TestCase
{
    #[TestDox('getPostTypes contains only "post" by default')]
    public function testGetPostTypes(): void
    {
        $config = new DefaultGetPostsConfig();
        $this->assertEquals(['post'], $config->getPostTypes());
    }
}
