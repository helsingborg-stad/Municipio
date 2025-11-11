<?php

namespace Municipio\PostsList\Config\GetPostsConfig;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class AbstractDecoratedGetPostsConfigTest extends TestCase
{
    #[TestDox('can be instantiated')]
    public function testCanBeInstantiated(): void
    {
        $instance = new class extends AbstractDecoratedGetPostsConfig{
        };

        $this->assertInstanceOf(AbstractDecoratedGetPostsConfig::class, $instance);
    }

    #[TestDox('proxies inner config')]
    public function testProxiesInnerConfig(): void
    {
        $instance = new class extends AbstractDecoratedGetPostsConfig{
            public function __construct(protected GetPostsConfigInterface $innerConfig = new DefaultGetPostsConfig())
            {
            }
        };

        $this->assertSame(['post'], $instance->getPostTypes());
    }
}
