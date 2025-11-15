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
        $innerConfig = new DefaultGetPostsConfig();
        $instance    = new class ($innerConfig) extends AbstractDecoratedGetPostsConfig{
            public function __construct(protected GetPostsConfigInterface $innerConfig)
            {
            }
        };

        $this->assertSame($innerConfig->getPostTypes(), $instance->getPostTypes());
        $this->assertSame($innerConfig->getPostsPerPage(), $instance->getPostsPerPage());
        $this->assertSame($innerConfig->getPage(), $instance->getPage());
        $this->assertSame($innerConfig->isFacettingTaxonomyQueryEnabled(), $instance->isFacettingTaxonomyQueryEnabled());
        $this->assertSame($innerConfig->getSearch(), $instance->getSearch());
        $this->assertSame($innerConfig->getOrderBy(), $instance->getOrderBy());
        $this->assertSame($innerConfig->getOrder(), $instance->getOrder());
        $this->assertSame($innerConfig->getDateFrom(), $instance->getDateFrom());
        $this->assertSame($innerConfig->getDateTo(), $instance->getDateTo());
        $this->assertSame($innerConfig->getTerms(), $instance->getTerms());
    }
}
