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
}
