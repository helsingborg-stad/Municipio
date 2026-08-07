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
            'header_sortable_section_main_lower' => ['logotype', 'primary'],
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
