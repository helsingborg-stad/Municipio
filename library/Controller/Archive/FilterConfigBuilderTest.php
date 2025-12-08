<?php

namespace Municipio\Controller\Archive;

use Municipio\PostsList\Config\FilterConfig\DefaultFilterConfig;
use Municipio\PostsList\Config\FilterConfig\FilterConfigInterface;
use PHPUnit\Framework\TestCase;

class FilterConfigBuilderTest extends TestCase
{
    public function testBuildReturnsFilterConfigInterface(): void
    {
        $config = (new FilterConfigBuilder())
            ->setEnabled(true)
            ->setResetUrl('https://example.com/reset')
            ->setDateFilterEnabled(true)
            ->setTextSearchEnabled(true)
            ->setTaxonomyFilterConfigs([])
            ->build();

        $this->assertInstanceOf(FilterConfigInterface::class, $config);
        $this->assertTrue($config->isEnabled());
        $this->assertEquals('https://example.com/reset', $config->getResetUrl());
        $this->assertTrue($config->isDateFilterEnabled());
        $this->assertTrue($config->isTextSearchEnabled());
        $this->assertEquals([], $config->getTaxonomiesEnabledForFiltering());
    }
}
