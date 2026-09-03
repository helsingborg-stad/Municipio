<?php

declare(strict_types=1);

namespace Municipio\Customizer\Sections\Menu;

use Municipio\Customizer\PanelsRegistry;
use PHPUnit\Framework\TestCase;

if (!function_exists(__NAMESPACE__ . '\\get_theme_mod')) {
    function get_theme_mod(string $setting, mixed $default = false): mixed
    {
        return BehaviourTestState::$themeMods[$setting] ?? $default;
    }
}

final class BehaviourTestState
{
    /** @var array<string, mixed> */
    public static array $themeMods = [];
}

final class BehaviourTest extends TestCase
{
    protected function setUp(): void
    {
        PanelsRegistry::getInstance()->fields = [];
        BehaviourTestState::$themeMods = [];
    }

    public function testDoesNotRegisterTheObsoleteDrawerScreenSizesField(): void
    {
        new Behaviour('municipio_customizer_section_menu');

        $registeredSettings = array_column(PanelsRegistry::getInstance()->getRegisteredFields(), 'settings');

        static::assertNotContains('drawer_screen_sizes', $registeredSettings);
    }

    public function testPrimaryMenuDropdownSettingsAreOnlyShownWhenPrimaryMenuIsInDesktopHeader(): void
    {
        new Behaviour('municipio_customizer_section_menu');

        $dropdownField = $this->getField('primary_menu_dropdown');
        $extendedField = $this->getField('primary_menu_dropdown_extended');

        static::assertIsCallable($dropdownField['active_callback']);
        static::assertIsCallable($extendedField['active_callback']);
        static::assertTrue(($dropdownField['active_callback'])());
        static::assertFalse(($extendedField['active_callback'])());

        BehaviourTestState::$themeMods = [
            'header_sortable_section_main_lower' => ['primary'],
            'primary_menu_dropdown' => true,
        ];

        static::assertTrue(($dropdownField['active_callback'])());
        static::assertTrue(($extendedField['active_callback'])());

        BehaviourTestState::$themeMods = [
            'header_sortable_section_main_upper' => ['logotype'],
            'header_sortable_section_main_lower' => ['language'],
            'primary_menu_dropdown' => true,
        ];

        static::assertFalse(($dropdownField['active_callback'])());
        static::assertFalse(($extendedField['active_callback'])());
    }

    /**
     * @return array<string, mixed>
     */
    private function getField(string $settings): array
    {
        foreach (PanelsRegistry::getInstance()->getRegisteredFields() as $field) {
            if (($field['settings'] ?? null) === $settings) {
                return $field;
            }
        }

        self::fail(sprintf('Field %s was not registered.', $settings));
    }
}
