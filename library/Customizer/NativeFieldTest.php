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
        PanelsRegistry::getInstance()->fields = [];
    }

    #[TestDox('getSettingArguments maps Customizer-shaped settings to native Customizer setting arguments')]
    public function testGetSettingArgumentsMapsCustomizerShapedSettingsToNativeCustomizerSettingArguments(): void
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

        $hiddenArguments = NativeField::getSettingArguments([
            'type' => 'hidden',
        ]);

        $this->assertIsCallable($hiddenArguments['sanitize_callback']);
        $this->assertSame('{"item":true}', $hiddenArguments['sanitize_callback']('{"item":true}'));
    }

    #[TestDox('getControlArguments maps Customizer slider fields to native number controls')]
    public function testGetControlArgumentsMapsCustomizerSliderFieldsToNativeNumberControls(): void
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

    #[TestDox('getControlArguments maps Customizer code fields to native code editor controls')]
    public function testGetControlArgumentsMapsCustomizerCodeFieldsToNativeCodeEditorControls(): void
    {
        $arguments = NativeField::getControlArguments([
            'type' => 'code',
            'section' => 'header',
            'choices' => [
                'language' => 'js',
            ],
        ]);

        $this->assertSame('code', $arguments['type']);
        $this->assertSame('application/javascript', $arguments['code_type']);
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

    #[TestDox('supports returns false for Customizer multi-select fields')]
    public function testSupportsReturnsFalseForMultiSelectFields(): void
    {
        $this->assertFalse(NativeField::supports([
            'type' => 'select',
            'settings' => 'native_test_multi_select',
            'multiple' => true,
        ]));

        $this->assertFalse(NativeField::supports([
            'type' => 'select',
            'settings' => 'native_test_numeric_multi_select',
            'multiple' => 6,
        ]));
    }

    #[TestDox('supports returns false for Customizer alpha color fields')]
    public function testSupportsReturnsFalseForAlphaColorFields(): void
    {
        $this->assertFalse(NativeField::supports([
            'type' => 'color',
            'settings' => 'native_test_alpha_color',
            'choices' => [
                'alpha' => true,
            ],
        ]));

        $this->assertTrue(NativeField::supports([
            'type' => 'hidden',
            'settings' => 'native_test_hidden',
        ]));
    }

    #[TestDox('CustomField routes and sanitizes Customizer-only fields implemented by Municipio controls')]
    public function testCustomFieldRoutesAndSanitizesCustomizerOnlyFieldsImplementedByMunicipioControls(): void
    {
        $this->assertTrue(CustomField::supports([
            'type' => 'multicheck',
            'settings' => 'custom_test_multicheck',
        ]));

        $this->assertTrue(CustomField::supports([
            'type' => 'select',
            'settings' => 'custom_test_multi_select',
            'multiple' => true,
        ]));

        $this->assertTrue(CustomField::supports([
            'type' => 'select',
            'settings' => 'custom_test_numeric_multi_select',
            'multiple' => 6,
        ]));

        $this->assertTrue(CustomField::supports([
            'type' => 'color',
            'settings' => 'custom_test_alpha_color',
            'choices' => [
                'alpha' => true,
            ],
        ]));

        $this->assertTrue(CustomField::supports([
            'type' => 'sortable',
            'settings' => 'custom_test_sortable',
        ]));

        CustomizerField::addField([
            'type' => 'multicheck',
            'settings' => 'custom_routed_field',
            'section' => 'custom_test_section',
            'choices' => [
                'one' => 'One',
                'two' => 'Two',
            ],
        ]);

        $fields = PanelsRegistry::getInstance()->getRegisteredFields();
        $registeredField = end($fields);
        $action = end(NativeFieldTestState::$actions);

        $this->assertSame('custom_routed_field', $registeredField['settings']);
        $this->assertSame(CustomField::FIELD_DRIVER, $registeredField[CustomField::FIELD_DRIVER_KEY]);
        $this->assertSame('customize_register', $action[0]);

        $this->assertSame(
            ['one' => 'Value', 'nested' => ['two' => 'Second']],
            CustomFieldSettingArguments::sanitizeJsonArray('{"one":"Value","nested":{"two":"Second"}}'),
        );
    }

    #[TestDox('CustomizerField addField routes native-compatible fields through NativeField')]
    public function testCustomizerFieldAddFieldRoutesNativeCompatibleFieldsThroughNativeField(): void
    {
        CustomizerField::addField([
            'type' => 'select',
            'settings' => 'native_routed_field',
            'section' => 'native_test_section',
            'choices' => [
                'one' => 'One',
                'two' => 'Two',
            ],
        ]);

        $fields = PanelsRegistry::getInstance()->getRegisteredFields();
        $registeredField = end($fields);
        $action = end(NativeFieldTestState::$actions);

        $this->assertSame('native_routed_field', $registeredField['settings']);
        $this->assertSame(NativeField::FIELD_DRIVER, $registeredField[NativeField::FIELD_DRIVER_KEY]);
        $this->assertSame('customize_register', $action[0]);
    }
}
