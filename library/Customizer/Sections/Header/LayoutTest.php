<?php

namespace Municipio\Customizer\Sections\Header;

use Municipio\Customizer\PanelsRegistry;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

if (!function_exists(__NAMESPACE__ . '\\get_nav_menu_locations')) {
    /**
     * Test double for WordPress get_nav_menu_locations.
     *
     * @return array<string, int>
     */
    function get_nav_menu_locations(): array
    {
        return ['main-menu' => 1];
    }
}

if (!function_exists(__NAMESPACE__ . '\\get_theme_mod')) {
    /**
     * Test double for WordPress get_theme_mod.
     *
     * @param string $setting Theme mod setting key.
     * @param mixed $default Default value.
     *
     * @return mixed
     */
    function get_theme_mod(string $setting, mixed $default = null): mixed
    {
        return match ($setting) {
            'header_sticky' => 'sticky',
            'header_sortable_section_main_lower' => ['logotype', 'primary'],
            'header_logo_scroll_shrink' => true,
            'header_sortable_hidden_storage' => [
                'header_sortable_section_main_lower' => [
                    'logotype' => [
                        'align' => 'left',
                        'margin' => 'none',
                    ],
                ],
            ],
            default => $default,
        };
    }
}

class LayoutTest extends TestCase
{
    protected function setUp(): void
    {
        PanelsRegistry::getInstance()->fields = [];
    }

    #[TestDox('Layout registers the logotype scroll animation field with controller output and a callable active callback')]
    public function testLayoutRegistersTheLogotypeScrollAnimationField(): void
    {
        new Layout('municipio_customizer_section_header_panel_layout');

        $field = $this->findFieldBySettings(
            PanelsRegistry::getInstance()->getRegisteredFields(),
            'header_logo_scroll_shrink',
        );

        $this->assertNotNull($field);
        $this->assertSame('checkbox_switch', $field['type']);
        $this->assertFalse($field['default']);
        $this->assertSame('controller', $field['output'][0]['type']);
        $this->assertIsCallable($field['active_callback']);
        $this->assertTrue($field['active_callback']());
    }

    #[TestDox('Layout registers the logotype overlap slider with float steps and a conditional active callback')]
    public function testLayoutRegistersTheLogotypeOverlapSliderField(): void
    {
        new Layout('municipio_customizer_section_header_panel_layout');

        $field = $this->findFieldBySettings(
            PanelsRegistry::getInstance()->getRegisteredFields(),
            'header_logo_overlap_multiplier',
        );

        $this->assertNotNull($field);
        $this->assertSame('slider', $field['type']);
        $this->assertSame(0.25, $field['default']);
        $this->assertSame(['min' => 0.25, 'max' => 0.85, 'step' => 0.05], $field['choices']);
        $this->assertSame('controller', $field['output'][0]['type']);
        $this->assertArrayNotHasKey('sanitize_callback', $field);
        $this->assertIsCallable($field['active_callback']);
        $this->assertTrue($field['active_callback']());
    }

    #[TestDox('Layout registers a hidden field for the stored logotype scroll aspect ratio')]
    public function testLayoutRegistersTheLogotypeScrollAspectRatioHiddenField(): void
    {
        new Layout('municipio_customizer_section_header_panel_layout');

        $field = $this->findFieldBySettings(
            PanelsRegistry::getInstance()->getRegisteredFields(),
            'header_logo_scroll_aspect_ratio',
        );

        $this->assertNotNull($field);
        $this->assertSame('hidden', $field['type']);
        $this->assertSame('', $field['default']);
        $this->assertSame('controller', $field['output'][0]['type']);
    }

    /**
     * Get a registered Customizer field by its setting identifier.
     *
     * @param array<int, array<string, mixed>> $fields Registered fields.
     * @param string $settings Setting identifier.
     *
     * @return array<string, mixed>|null
     */
    private function findFieldBySettings(array $fields, string $settings): ?array
    {
        foreach ($fields as $field) {
            if (($field['settings'] ?? null) === $settings) {
                return $field;
            }
        }

        return null;
    }
}
