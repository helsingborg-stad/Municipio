<?php

namespace Municipio\Upgrade\V41;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class Version41Test extends TestCase
{
    #[TestDox('performs migration of customizer settings to design tokens')]
    public function testMigrateCustomizerSettingsToDesignTokens(): void
    {
        $themeMods = [
            'color_background' => ['background' => '#000001'],
            'color_text' => ['base' => '#000002'],
            'color_palette_primary' => ['base' => '#000003', 'contrasting' => '#000004'],
            'color_palette_secondary' => ['base' => '#000005', 'contrasting' => '#000006'],
            'color_card' => ['background' => '#000007'],
            'color_alpha' => ['base' => '#000008', 'contrasting' => '#000009'],
            'footer_subfooter_colors' => ['background' => '#000010', 'text' => '#000011'],
            'footer_background' => ['background-color' => '#000012'],
            'footer_color_text' => '#000013',
            'typography_base' => ['font-family' => 'Arial, sans-serif', 'font-size' => '18px'],
            'typography_h1' => ['font-family' => 'Georgia, serif'],
            'drop_shadow_color' => 'rgba(0, 0, 0, 0.06)',
            'drop_shadow_amount' => '2.5',
            'radius_md' => 8,
            'container' => '1280',
            'footer_logotype_height' => '64px',
            'color_button_primary' => ['base' => '#000014', 'contrasting' => '#000015'],
            'header_logotype_height' => '72px',
            'header_brand-color' => '#000016',
            'border_width_outline' => '3px',
            'field_border_radius' => '4px',
            'color_input' => ['border' => '#000017'],
        ];

        $migratedTokens = (new Version41(new FakeWpService()))->getMigratedTokens($themeMods);

        $expectedTokens = [
            '--color--background' => '#000001',
            '--color--surface-contrast' => '#000002',
            '--color--background-contrast' => '#000002',
            '--color--primary' => '#000003',
            '--color--primary-contrast' => '#000004',
            '--color--secondary' => '#000005',
            '--color--secondary-contrast' => '#000006',
            '--color--surface' => '#000007',
            '--color--alpha' => '#000008',
            '--color--alpha-contrast' => '#000009',
            '--c-footer--subfooter-color-background' => '#000010',
            '--c-footer--subfooter-color-text' => '#000011',
            '--c-footer--color--surface' => '#000012',
            '--c-footer--color--surface-contrast' => '#000013',
            '--font-family-base' => 'Arial, sans-serif',
            '--base-font-size' => '18px',
            '--font-family-heading' => 'Georgia, serif',
            '--shadow-color' => 'rgba(0, 0, 0, 0.06)',
            '--shadow-amount' => '2.5',
            '--border-radius' => 1.0,
            '--container-width' => '1280px',
            '--c-footer--logotype-height' => '64px',
            '--c-button--color--primary' => '#000014',
            '--c-button--color--primary-contrast' => '#000015',
            '--c-header--logotype-height' => '72px',
            '--c-header--brand-color' => '#000016',
            '--border-width' => '3px',
            '--c-field--border-radius' => '4px',
            '--c-field--color--surface-border' => '#000017',
        ];

        foreach ($expectedTokens as $tokenName => $expectedValue) {
            static::assertSame($expectedValue, $migratedTokens['token'][$tokenName]);
        }

        static::assertSame([], $migratedTokens['component']);
    }
}
