<?php

namespace Municipio\StyleguideCss\ThemeSettingsMapper;

use Municipio\StyleguideCss\CssVariables\CssVariablesCollectionInterface;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class ThemeSettingsMapperTest extends TestCase
{
    #[TestDox('returns a CssVariablesCollectionInterface instance')]
    public function testMapReturnsCssVariablesCollectionInterface(): void
    {
        $mapper = new ThemeSettingsMapper();
        $result = $mapper->map([]);
        $this->assertInstanceOf(CssVariablesCollectionInterface::class, $result);
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
        ];

        $mapper = new ThemeSettingsMapper();
        $result = $mapper->map($themeMods);

        $this->assertInstanceOf(CssVariablesCollectionInterface::class, $result);
        $this->assertStringContainsString('--color--background: #000001', (string) $result);
        $this->assertStringContainsString('--color--primary: #000002', (string) $result);
        $this->assertStringContainsString('--color--primary-contrast: #000003', (string) $result);
        $this->assertStringContainsString('--color--secondary: #000004', (string) $result);
        $this->assertStringContainsString('--color--secondary-contrast: #000005', (string) $result);
        $this->assertStringContainsString('--color--surface: #000006', (string) $result);
        $this->assertStringContainsString('--color--surface-contrast: #000007', (string) $result);
        $this->assertStringContainsString('--color--background-contrast: #000007', (string) $result);
        $this->assertStringContainsString('--color--alpha: #000008', (string) $result);
        $this->assertStringContainsString('--color--alpha-contrast: #000009', (string) $result);
        $this->assertStringContainsString('--c-footer--subfooter-color-background: #000010', (string) $result);
        $this->assertStringContainsString('--c-footer--subfooter-color-text: #000011', (string) $result);
    }
}
