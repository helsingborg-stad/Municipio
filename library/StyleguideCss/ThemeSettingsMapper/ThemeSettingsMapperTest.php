<?php

namespace Municipio\StyleguideCss\ThemeSettingsMapper;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class ThemeSettingsMapperTest extends TestCase
{
    #[TestDox('returns an array of CSS variables')]
    public function testMapReturnsArrayOfCssVariables(): void
    {
        $mapper = new ThemeSettingsMapper();
        $result = $mapper->map([]);
        $this->assertIsArray($result);
    }

    #[TestDox('maps theme settings to CSS variables correctly')]
    public function testMapMapsThemeSettingsToCssVariables(): void
    {
        $themeMods = [
            'color_background' => [
                'background' => '#000001',
            ],
            'color_palette_primary' => [
                'base' => '#000002',
                'contrasting' => '#000003',
            ],
            'color_palette_secondary' => [
                'base' => '#000004',
                'contrasting' => '#000005',
            ],
            'color_card' => [
                'background' => '#000006',
            ],
            'color_text' => [
                'base' => '#000007',
            ],
            'color_alpha' => [
                'base' => '#000008',
                'contrasting' => '#000009',
            ],
            'footer_subfooter_colors' => [
                'background' => '#000010',
                'text' => '#000011',
            ],
            'footer_background' => [
                'background-color' => '#000012',
            ],
            'footer_color_text' => '#000013',
            'typography_base' => [
                'font-family' => 'Arial, sans-serif',
                'font-size' => '18px',
            ],
            'typography_heading' => [
                'font-family' => 'Georgia, serif',
            ],
            'drop_shadow_color' => 'rgba(0, 0, 0, 0.06)',
            'drop_shadow_amount' => '2.5',
            'radius_md' => 7,
        ];

        $mapper = new ThemeSettingsMapper();
        $cssVariables = $mapper->map($themeMods);
        $cssVariablesAsString = implode(' ', array_map(fn($var) => (string) $var, $cssVariables));

        $this->assertStringContainsString('--color--background: #000001', $cssVariablesAsString);
        $this->assertStringContainsString('--color--primary: #000002', $cssVariablesAsString);
        $this->assertStringContainsString('--color--primary-contrast: #000003', $cssVariablesAsString);
        $this->assertStringContainsString('--color--secondary: #000004', $cssVariablesAsString);
        $this->assertStringContainsString('--color--secondary-contrast: #000005', $cssVariablesAsString);
        $this->assertStringContainsString('--color--surface: #000006', $cssVariablesAsString);
        $this->assertStringContainsString('--color--surface-contrast: #000007', $cssVariablesAsString);
        $this->assertStringContainsString('--color--background-contrast: #000007', $cssVariablesAsString);
        $this->assertStringContainsString('--color--alpha: #000008', $cssVariablesAsString);
        $this->assertStringContainsString('--color--alpha-contrast: #000009', $cssVariablesAsString);
        $this->assertStringContainsString('--c-footer--subfooter-color-background: #000010', $cssVariablesAsString);
        $this->assertStringContainsString('--c-footer--subfooter-color-text: #000011', $cssVariablesAsString);
        $this->assertStringContainsString('--c-footer--color--surface: #000012', $cssVariablesAsString);
        $this->assertStringContainsString('--c-footer--color--surface-contrast: #000013', $cssVariablesAsString);
        $this->assertStringContainsString('--font-family-base: Arial, sans-serif', $cssVariablesAsString);
        $this->assertStringContainsString('--base-font-size: 18px', $cssVariablesAsString);
        $this->assertStringContainsString('--font-family-heading: Georgia, serif', $cssVariablesAsString);
        $this->assertStringContainsString('--shadow-color: rgba(0, 0, 0, 0.06)', $cssVariablesAsString);
        $this->assertStringContainsString('--shadow-amount: 2.5', $cssVariablesAsString);
        $this->assertStringContainsString('--border-radius: 7', $cssVariablesAsString);
    }
}
