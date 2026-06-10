<?php

namespace Municipio\Customizer;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class KirkiFieldTest extends TestCase
{
    protected function setUp(): void
    {
        $this->resetNativeFieldState();
    }

    protected function tearDown(): void
    {
        $this->resetNativeFieldState();
    }

    #[TestDox('simple fields are detected as native customizer fields')]
    public function testSimpleFieldsAreDetectedAsNativeCustomizerFields(): void
    {
        $field = [
            'type'     => 'select',
            'settings' => 'test_select_setting',
            'label'    => 'Test select',
            'section'  => 'test_section',
            'default'  => 'one',
            'choices'  => [
                'one' => 'One',
                'two' => 'Two',
            ],
        ];

        $this->assertTrue(KirkiField::isNativeField($field));
    }

    #[TestDox('kirki specific fields are not detected as native customizer fields')]
    public function testKirkiSpecificFieldsAreNotDetectedAsNativeCustomizerFields(): void
    {
        $this->assertFalse(KirkiField::isNativeField([
            'type'     => 'switch',
            'settings' => 'test_switch_setting',
        ]));

        $this->assertFalse(KirkiField::isNativeField([
            'type'            => 'select',
            'settings'        => 'test_conditional_select_setting',
            'active_callback' => [
                [
                    'setting'  => 'another_setting',
                    'operator' => '===',
                    'value'    => 'enabled',
                ],
            ],
        ]));
    }

    #[TestDox('native fields are registered with native customizer setting and control args')]
    public function testNativeFieldsAreRegisteredWithNativeCustomizerSettingAndControlArgs(): void
    {
        KirkiField::addField([
            'type'        => 'radio',
            'settings'    => 'test_radio_setting',
            'label'       => 'Test radio',
            'description' => 'Test description',
            'section'     => 'test_section',
            'default'     => 'one',
            'priority'    => 10,
            'choices'     => [
                'one' => 'One',
                'two' => 'Two',
            ],
        ]);

        $wpCustomize = new FakeWpCustomizeManager();

        KirkiField::registerNativeFields($wpCustomize);

        $this->assertArrayHasKey('test_radio_setting', $wpCustomize->settings);
        $this->assertSame('one', $wpCustomize->settings['test_radio_setting']['default']);
        $this->assertSame('theme_mod', $wpCustomize->settings['test_radio_setting']['type']);
        $this->assertIsCallable($wpCustomize->settings['test_radio_setting']['sanitize_callback']);

        $this->assertArrayHasKey('test_radio_setting', $wpCustomize->controls);
        $this->assertSame('radio', $wpCustomize->controls['test_radio_setting']['type']);
        $this->assertSame('Test radio', $wpCustomize->controls['test_radio_setting']['label']);
        $this->assertSame('test_section', $wpCustomize->controls['test_radio_setting']['section']);
    }

    private function resetNativeFieldState(): void
    {
        $reflection = new ReflectionClass(KirkiField::class);

        $nativeFields = $reflection->getProperty('nativeFields');
        $nativeFields->setAccessible(true);
        $nativeFields->setValue(null, []);

        $nativeFieldsHookAdded = $reflection->getProperty('nativeFieldsHookAdded');
        $nativeFieldsHookAdded->setAccessible(true);
        $nativeFieldsHookAdded->setValue(null, false);
    }
}

class FakeWpCustomizeManager
{
    public array $settings = [];
    public array $controls = [];

    public function add_setting(string $id, array $args): void
    {
        $this->settings[$id] = $args;
    }

    public function add_control($id, array $args = []): void
    {
        $this->controls[$id] = $args;
    }
}
