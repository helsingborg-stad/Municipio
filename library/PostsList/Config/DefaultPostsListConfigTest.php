<?php

namespace Municipio\PostsList\Config;

use Municipio\PostsList\Config\PostsListAppearanceConfig\DefaultPostsListAppearanceConfig;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class DefaultPostsListConfigTest extends TestCase
{
    #[TestDox('getPostTypes contains only "post" by default')]
    public function testGetPostTypes(): void
    {
        $config = new DefaultPostsListConfig();
        $this->assertEquals(['post'], $config->getPostTypes());
    }

    #[TestDox('getAppearanceConfig returns DefaultPostsListAppearanceConfig instance')]
    public function testGetAppearanceConfig(): void
    {
        $config = new DefaultPostsListConfig();
        $this->assertInstanceOf(DefaultPostsListAppearanceConfig::class, $config->getAppearanceConfig());
    }
}
