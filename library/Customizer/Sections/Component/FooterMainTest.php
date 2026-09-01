<?php

namespace Municipio\Customizer\Sections\Component;

use Municipio\Customizer\PanelsRegistry;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class FooterMainTest extends TestCase
{
    protected function setUp(): void
    {
        PanelsRegistry::getInstance()->fields = [];
    }

    #[TestDox('Footer main registers the main footer logotype and footer background image fields only')]
    public function testFooterMainRegistersTheMainFooterLogotypeAndFooterBackgroundImageFieldsOnly(): void
    {
        new FooterMain('municipio_customizer_section_component_footer');

        $fields = PanelsRegistry::getInstance()->getRegisteredFields();

        $this->assertNull($this->findFieldBySettings($fields, 'footer_style'));
        $this->assertNull($this->findFieldBySettings($fields, 'footer_columns'));

        $logotypeField = $this->findFieldBySettings($fields, 'footer_logotype');
        $backgroundImageField = $this->findFieldBySettings($fields, 'footer_background_image');

        $this->assertNotNull($logotypeField);
        $this->assertNotNull($backgroundImageField);
        $this->assertSame('select', $logotypeField['type']);
        $this->assertSame('refresh', $logotypeField['transport']);
        $this->assertSame('upload', $backgroundImageField['type']);
        $this->assertSame('refresh', $backgroundImageField['transport']);
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
