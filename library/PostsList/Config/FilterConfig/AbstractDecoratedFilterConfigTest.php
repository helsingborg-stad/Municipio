<?php

namespace Municipio\PostsList\Config\FilterConfig;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class AbstractDecoratedFilterConfigTest extends TestCase
{
    #[TestDox('can be instantiated')]
    public function testCanBeInstantiated(): void
    {
        $instance = new class extends AbstractDecoratedFilterConfig{
        };

        $this->assertInstanceOf(AbstractDecoratedFilterConfig::class, $instance);
    }

    #[TestDox('proxies inner config')]
    public function testProxiesInnerConfig(): void
    {
        $innerConfig = new DefaultFilterConfig();
        $instance    = new class ($innerConfig) extends AbstractDecoratedFilterConfig{
            public function __construct(protected FilterConfigInterface $innerConfig)
            {
            }
        };

        $this->assertSame($innerConfig->isEnabled(), $instance->isEnabled());
        $this->assertSame($innerConfig->isTextSearchEnabled(), $instance->isTextSearchEnabled());
        $this->assertSame($innerConfig->isDateFilterEnabled(), $instance->isDateFilterEnabled());
        $this->assertSame($innerConfig->getTaxonomiesEnabledForFiltering(), $instance->getTaxonomiesEnabledForFiltering());
        $this->assertSame($innerConfig->showReset(), $instance->showReset());
        $this->assertSame($innerConfig->getResetUrl(), $instance->getResetUrl());
    }
}
