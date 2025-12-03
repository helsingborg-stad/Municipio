<?php

namespace Municipio\PostsList\Config\FilterConfig;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class DefaultFilterConfigTest extends TestCase
{
    #[TestDox('isEnabled() returns false by default')]
    public function testIsEnabledReturnsFalseByDefault(): void
    {
        $filterConfig = new DefaultFilterConfig();
        $this->assertFalse($filterConfig->isEnabled());
    }

    #[TestDox('showReset() returns false by default')]
    public function testShowResetReturnsFalseByDefault(): void
    {
        $filterConfig = new DefaultFilterConfig();
        $this->assertFalse($filterConfig->showReset());
    }

    #[TestDox('getResetUrl() returns null by default')]
    public function testGetResetUrlReturnsNullByDefault(): void
    {
        $filterConfig = new DefaultFilterConfig();
        $this->assertNull($filterConfig->getResetUrl());
    }

    #[TestDox('isTextSearchEnabled() returns false by default')]
    public function testIsTextSearchEnabledReturnsFalseByDefault(): void
    {
        $filterConfig = new DefaultFilterConfig();
        $this->assertFalse($filterConfig->isTextSearchEnabled());
    }

    #[TestDox('isDateFilterEnabled() returns false by default')]
    public function testIsDateFilterEnabledReturnsFalseByDefault(): void
    {
        $filterConfig = new DefaultFilterConfig();
        $this->assertFalse($filterConfig->isDateFilterEnabled());
    }

    #[TestDox('getTaxonomiesEnabledForFiltering() returns empty array by default')]
    public function testGetTaxonomiesEnabledForFilteringReturnsEmptyArrayByDefault(): void
    {
        $filterConfig = new DefaultFilterConfig();
        $this->assertEmpty($filterConfig->getTaxonomiesEnabledForFiltering());
    }
}
