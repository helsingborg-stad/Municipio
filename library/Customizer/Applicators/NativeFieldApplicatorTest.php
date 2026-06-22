<?php

namespace Municipio\Customizer\Applicators {
    use Municipio\Customizer\Applicators\Types\Controller;
    use Municipio\Customizer\NativeField;
    use Municipio\Customizer\PanelsRegistry;
    use PHPUnit\Framework\Attributes\TestDox;
    use PHPUnit\Framework\TestCase;
    use WpService\Implementations\FakeWpService;

    if (!function_exists(__NAMESPACE__ . '\\get_theme_mod')) {
        /**
         * Test double for WordPress' get_theme_mod function.
         *
         * @param string $settingKey Setting key.
         * @param mixed  $default    Default value.
         *
         * @return mixed
         */
        function get_theme_mod(string $settingKey, mixed $default = false): mixed
        {
            return NativeFieldApplicatorTestState::$themeMods[$settingKey] ?? $default;
        }
    }

    class NativeFieldApplicatorTestState
    {
        /**
         * @var array<string, mixed>
         */
        public static array $themeMods = [];
    }

    class ExposedFieldsApplicator extends AbstractApplicator
    {
        /**
         * Expose registered fields for tests.
         *
         * @return array
         */
        public function exposedFields(): array
        {
            return $this->getFields();
        }
    }

    class ExposedFieldValueApplicator extends AbstractApplicator
    {
        /**
         * Expose field value lookup for tests.
         *
         * @param string $settingKey The setting key to retrieve.
         * @param array  $field      Field configuration.
         *
         * @return mixed
         */
        public function exposedFieldValue(string $settingKey, array $field): mixed
        {
            return $this->getFieldValue($settingKey, $field);
        }
    }

    class NativeFieldApplicatorTest extends TestCase
    {
        protected function setUp(): void
        {
            NativeFieldApplicatorTestState::$themeMods = [];
            PanelsRegistry::getInstance()->fields = [];
        }

        #[TestDox('getFields includes native fields registered through the panel registry')]
        public function testGetFieldsIncludesNativeFieldsRegisteredThroughThePanelRegistry(): void
        {
            PanelsRegistry::getInstance()->addRegisteredField([
                'type' => 'text',
                'settings' => 'native_applicator_field',
                'section' => 'native_applicator_section',
                NativeField::FIELD_DRIVER_KEY => NativeField::FIELD_DRIVER,
                'output' => [
                    [
                        'type' => 'controller',
                    ],
                ],
            ]);

            $applicator = new ExposedFieldsApplicator();

            $fields = $applicator->exposedFields();

            $this->assertArrayHasKey('native_applicator_field', $fields);
            $this->assertSame(NativeField::FIELD_DRIVER, $fields['native_applicator_field'][NativeField::FIELD_DRIVER_KEY]);
        }

        #[TestDox('getFieldValue reads native field values from theme mods with defaults')]
        public function testGetFieldValueReadsNativeFieldValuesFromThemeModsWithDefaults(): void
        {
            NativeFieldApplicatorTestState::$themeMods['native_field_value'] = 'stored value';

            $applicator = new ExposedFieldValueApplicator();

            $this->assertSame('stored value', $applicator->exposedFieldValue('native_field_value', [
                NativeField::FIELD_DRIVER_KEY => NativeField::FIELD_DRIVER,
                'default' => 'default value',
            ]));

            $this->assertSame('default value', $applicator->exposedFieldValue('missing_native_field_value', [
                NativeField::FIELD_DRIVER_KEY => NativeField::FIELD_DRIVER,
                'default' => 'default value',
            ]));
        }

        #[TestDox('controller applicator output stays the same after a field migrates to native')]
        public function testControllerApplicatorOutputStaysTheSameAfterFieldMigratesToNative(): void
        {
            NativeFieldApplicatorTestState::$themeMods['archive_post_heading'] = 'Migrated heading';

            $legacyOutput = $this->getControllerApplicatorData($this->getArchiveHeadingField());
            $nativeOutput = $this->getControllerApplicatorData([
                ...$this->getArchiveHeadingField(),
                NativeField::FIELD_DRIVER_KEY => NativeField::FIELD_DRIVER,
            ]);

            $this->assertEquals($legacyOutput, $nativeOutput);
            $this->assertSame('Migrated heading', $nativeOutput->archivePost['heading']);
        }

        /**
         * Get controller applicator data for a single registered field.
         *
         * @param array $field Field configuration.
         *
         * @return object
         */
        private function getControllerApplicatorData(array $field): object
        {
            PanelsRegistry::getInstance()->fields = [];
            PanelsRegistry::getInstance()->addRegisteredField($field);

            return (new Controller(new FakeWpService()))->getData();
        }

        /**
         * Get the migrated Archive heading field configuration.
         *
         * @return array
         */
        private function getArchiveHeadingField(): array
        {
            return [
                'type' => 'text',
                'settings' => 'archive_post_heading',
                'section' => 'municipio_customizer_panel_archive_post',
                'default' => '',
                'output' => [
                    [
                        'type' => 'controller',
                        'as_object' => true,
                    ],
                ],
            ];
        }
    }
}
