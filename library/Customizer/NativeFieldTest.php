<?php

namespace Municipio\Customizer;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

if (!function_exists(__NAMESPACE__ . '\\add_action')) {
    /**
     * Test double for WordPress' add_action function.
     *
     * @param string   $hookName Hook name.
     * @param callable $callback Hook callback.
     *
     * @return void
     */
    function add_action(string $hookName, callable $callback): void
    {
        NativeFieldTestState::$actions[] = [$hookName, $callback];
    }
}

class NativeFieldTestState
{
    /**
     * @var array<int, array{0: string, 1: callable}>
     */
    public static array $actions = [];
}

class NativeFieldTest extends TestCase
{
    protected function setUp(): void
    {
        NativeFieldTestState::$actions = [];
    }

    #[TestDox('getSettingArguments maps Kirki-shaped settings to native Customizer setting arguments')]
    public function testGetSettingArgumentsMapsKirkiShapedSettingsToNativeCustomizerSettingArguments(): void
    {
        $arguments = NativeField::getSettingArguments([
            'type' => 'slider',
            'default' => 7,
            'transport' => 'postMessage',
        ]);

        $this->assertSame('theme_mod', $arguments['type']);
        $this->assertSame('edit_theme_options', $arguments['capability']);
        $this->assertSame(7, $arguments['default']);
        $this->assertSame('postMessage', $arguments['transport']);
        $this->assertSame('absint', $arguments['sanitize_callback']);
    }

    #[TestDox('getControlArguments maps Kirki slider fields to native number controls')]
    public function testGetControlArgumentsMapsKirkiSliderFieldsToNativeNumberControls(): void
    {
        $arguments = NativeField::getControlArguments([
            'type' => 'slider',
            'section' => 'archive',
            'label' => 'Posts per page',
            'description' => 'How many posts should be shown.',
            'choices' => [
                'min' => 1,
                'max' => 12,
                'step' => 1,
            ],
        ]);

        $this->assertSame('number', $arguments['type']);
        $this->assertSame('archive', $arguments['section']);
        $this->assertSame('Posts per page', $arguments['label']);
        $this->assertSame('How many posts should be shown.', $arguments['description']);
        $this->assertSame(['min' => 1, 'max' => 12, 'step' => 1], $arguments['input_attrs']);
    }

    #[TestDox('addField stores native fields for applicators and schedules native registration')]
    public function testAddFieldStoresNativeFieldsForApplicatorsAndSchedulesNativeRegistration(): void
    {
        NativeField::addField([
            'type' => 'text',
            'settings' => 'native_test_field',
            'section' => 'native_test_section',
            'output' => [
                [
                    'type' => 'controller',
                ],
            ],
        ]);

        $fields = PanelsRegistry::getInstance()->getRegisteredFields();
        $registeredField = end($fields);

        $this->assertSame('native_test_field', $registeredField['settings']);
        $this->assertSame(NativeField::FIELD_DRIVER, $registeredField[NativeField::FIELD_DRIVER_KEY]);

        $action = end(NativeFieldTestState::$actions);

        $this->assertSame('customize_register', $action[0]);
    }
}
