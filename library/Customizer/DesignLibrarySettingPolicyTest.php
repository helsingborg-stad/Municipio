<?php

declare(strict_types=1);

namespace Municipio\Customizer;

use PHPUnit\Framework\TestCase;

class DesignLibrarySettingPolicyTest extends TestCase
{
    public function testAllowsExplicitExactKey(): void
    {
        $this->assertTrue(DesignLibrarySettingPolicy::isAllowedSettingKey('tokens'));
    }

    public function testAllowsExplicitPrefixedKey(): void
    {
        $this->assertTrue(DesignLibrarySettingPolicy::isAllowedSettingKey('header_layout'));
    }

    public function testDeniesUndeclaredKey(): void
    {
        $this->assertFalse(DesignLibrarySettingPolicy::isAllowedSettingKey('siteurl'));
    }
}
