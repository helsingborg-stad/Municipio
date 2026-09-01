<?php

namespace Municipio\Customizer\Sections\Component;

use Municipio\Customizer\PanelsRegistry;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class FooterTest extends TestCase
{
    protected function setUp(): void
    {
        PanelsRegistry::getInstance()->fields = [];
    }

    #[TestDox('Footer registers main footer and subfooter fields in the same merged section')]
    public function testFooterRegistersMainFooterAndSubfooterFieldsInTheSameMergedSection(): void
    {
        new Footer('municipio_customizer_section_component_footer');

        $fields = PanelsRegistry::getInstance()->getRegisteredFields();

        $mainFooterField = $this->getFieldBySettings($fields, 'footer_logotype');
        $subfooterField = $this->getFieldBySettings($fields, 'footer_subfooter_logotype');
        $backgroundImageField = $this->getFieldBySettings($fields, 'footer_background_image');

        $this->assertSame('municipio_customizer_section_component_footer', $mainFooterField['section']);
        $this->assertSame('municipio_customizer_section_component_footer', $subfooterField['section']);
        $this->assertSame('municipio_customizer_section_component_footer', $backgroundImageField['section']);
    }

    /**
     * Get a registered Customizer field by its setting identifier.
     *
     * @param array<int, array<string, mixed>> $fields Registered fields.
     * @param string $settings Setting identifier.
     *
     * @return array<string, mixed>
     */
    private function getFieldBySettings(array $fields, string $settings): array
    {
        foreach ($fields as $field) {
            if (($field['settings'] ?? null) === $settings) {
                return $field;
            }
        }

        $this->fail(sprintf('Expected field with settings "%s" to be registered.', $settings));
    }
}