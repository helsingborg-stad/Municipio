<?php

declare(strict_types=1);

namespace Municipio\Controller\Archive;

use Municipio\PostsList\Config\FilterConfig\FilterConfigInterface;
use PHPUnit\Framework\TestCase;

class FilterConfigBuilderTest extends TestCase
{
    public function testBuildReturnsFilterConfigInterface(): void
    {
        $config = (new FilterConfigBuilder())
            ->setResetUrl('https://example.com/reset')
            ->setDateFilterEnabled(true)
            ->setTextSearchEnabled(true)
            ->setTaxonomyFilterConfigs([])
            ->build();

        static::assertInstanceOf(FilterConfigInterface::class, $config);
        static::assertSame('https://example.com/reset', $config->getResetUrl());
        static::assertTrue($config->isDateFilterEnabled());
        static::assertTrue($config->isTextSearchEnabled());
        static::assertSame([], $config->getTaxonomiesEnabledForFiltering());
    }
}
