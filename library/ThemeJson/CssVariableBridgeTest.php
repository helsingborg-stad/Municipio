<?php

namespace Municipio\ThemeJson;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class CssVariableBridgeTest extends TestCase
{
    #[TestDox('constructor registers wp_head and admin_head actions')]
    public function testConstructorRegistersActions(): void
    {
        $wpService = new FakeWpService(['addAction' => true]);

        new CssVariableBridge($wpService);

        $this->assertCount(2, $wpService->methodCalls['addAction'] ?? []);
        $this->assertEquals('wp_head', $wpService->methodCalls['addAction'][0][0]);
        $this->assertEquals('admin_head', $wpService->methodCalls['addAction'][1][0]);
    }

    #[TestDox('outputCssVariableBridge outputs CSS with variable mappings')]
    public function testOutputCssVariableBridgeOutputsCss(): void
    {
        $wpService = new FakeWpService(['addAction' => true]);
        $bridge    = new CssVariableBridge($wpService);

        ob_start();
        $bridge->outputCssVariableBridge();
        $output = ob_get_clean();

        // Check that output contains the style tag
        $this->assertStringContainsString('<style id="theme-json-css-bridge">', $output);
        $this->assertStringContainsString('</style>', $output);

        // Check that it contains CSS variable mappings
        $this->assertStringContainsString('--color-primary:', $output);
        $this->assertStringContainsString('var(--wp--preset--color--primary', $output);

        // Check that it includes fallback values
        $this->assertStringContainsString('#ae0b05', $output); // Primary default
    }

    #[TestDox('outputCssVariableBridge includes all required color mappings')]
    public function testOutputCssVariableBridgeIncludesAllMappings(): void
    {
        $wpService = new FakeWpService(['addAction' => true]);
        $bridge    = new CssVariableBridge($wpService);

        ob_start();
        $bridge->outputCssVariableBridge();
        $output = ob_get_clean();

        // Primary colors
        $this->assertStringContainsString('--color-primary:', $output);
        $this->assertStringContainsString('--color-primary-dark:', $output);
        $this->assertStringContainsString('--color-primary-light:', $output);
        $this->assertStringContainsString('--color-primary-contrasting:', $output);

        // Secondary colors
        $this->assertStringContainsString('--color-secondary:', $output);

        // Background and text
        $this->assertStringContainsString('--color-background:', $output);
        $this->assertStringContainsString('--color-base:', $output);

        // State colors
        $this->assertStringContainsString('--color-success:', $output);
        $this->assertStringContainsString('--color-warning:', $output);
        $this->assertStringContainsString('--color-danger:', $output);
        $this->assertStringContainsString('--color-info:', $output);
    }
}
