<?php

declare(strict_types=1);

namespace Municipio\Api\Customize\Config;

use PHPUnit\Framework\TestCase;
use WpService\Contracts\ApplyFilters;

class CustomizeConfigTest extends TestCase
{
    public function testGetThemeModKeyReturnsDefaultWhenNotFiltered(): void
    {
        $wpService = new class implements ApplyFilters {
            public function applyFilters(string $hookName, mixed $value, mixed ...$args): mixed
            {
                return $value;
            }
        };

        $config = new CustomizeConfig($wpService);

        $this->assertSame('tokens', $config->getThemeModKey());
    }

    public function testGetThemeModKeyReturnsFilteredValue(): void
    {
        $wpService = new class implements ApplyFilters {
            public function applyFilters(string $hookName, mixed $value, mixed ...$args): mixed
            {
                return $hookName === 'Municipio/Api/Customize/getThemeModKey' ? 'custom_tokens' : $value;
            }
        };

        $config = new CustomizeConfig($wpService);

        $this->assertSame('custom_tokens', $config->getThemeModKey());
    }

    public function testGetThemeModKeyFallsBackToDefaultOnInvalidFilteredValue(): void
    {
        $wpService = new class implements ApplyFilters {
            public function applyFilters(string $hookName, mixed $value, mixed ...$args): mixed
            {
                return ['invalid'];
            }
        };

        $config = new CustomizeConfig($wpService);

        $this->assertSame('tokens', $config->getThemeModKey());
    }

    public function testGetPermissionCapabilityDefaultsToCurrentCapability(): void
    {
        $wpService = new class implements ApplyFilters {
            public function applyFilters(string $hookName, mixed $value, mixed ...$args): mixed
            {
                return $value;
            }
        };

        $config = new CustomizeConfig($wpService);

        $this->assertSame('edit_theme_options', $config->getGetPermissionCapability());
    }

    public function testSavePermissionCapabilityDefaultsToCurrentCapability(): void
    {
        $wpService = new class implements ApplyFilters {
            public function applyFilters(string $hookName, mixed $value, mixed ...$args): mixed
            {
                return $value;
            }
        };

        $config = new CustomizeConfig($wpService);

        $this->assertSame('edit_theme_options', $config->getSavePermissionCapability());
    }

    public function testGetPermissionCapabilityCanBeFiltered(): void
    {
        $wpService = new class implements ApplyFilters {
            public function applyFilters(string $hookName, mixed $value, mixed ...$args): mixed
            {
                return $hookName === 'Municipio/Api/Customize/getGetPermissionCapability' ? 'read' : $value;
            }
        };

        $config = new CustomizeConfig($wpService);

        $this->assertSame('read', $config->getGetPermissionCapability());
    }

    public function testSavePermissionCapabilityCanBeFiltered(): void
    {
        $wpService = new class implements ApplyFilters {
            public function applyFilters(string $hookName, mixed $value, mixed ...$args): mixed
            {
                return $hookName === 'Municipio/Api/Customize/getSavePermissionCapability' ? 'manage_options' : $value;
            }
        };

        $config = new CustomizeConfig($wpService);

        $this->assertSame('manage_options', $config->getSavePermissionCapability());
    }

    public function testGetPermissionCapabilityFallsBackToCurrentCapabilityOnInvalidFilteredValue(): void
    {
        $wpService = new class implements ApplyFilters {
            public function applyFilters(string $hookName, mixed $value, mixed ...$args): mixed
            {
                if ($hookName === 'Municipio/Api/Customize/getGetPermissionCapability') {
                    return ['invalid'];
                }

                return $value;
            }
        };

        $config = new CustomizeConfig($wpService);

        $this->assertSame('edit_theme_options', $config->getGetPermissionCapability());
    }

    public function testSavePermissionCapabilityFallsBackToCurrentCapabilityOnInvalidFilteredValue(): void
    {
        $wpService = new class implements ApplyFilters {
            public function applyFilters(string $hookName, mixed $value, mixed ...$args): mixed
            {
                if ($hookName === 'Municipio/Api/Customize/getSavePermissionCapability') {
                    return null;
                }

                return $value;
            }
        };

        $config = new CustomizeConfig($wpService);

        $this->assertSame('edit_theme_options', $config->getSavePermissionCapability());
    }
}
