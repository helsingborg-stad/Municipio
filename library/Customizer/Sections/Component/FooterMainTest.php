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

    #[TestDox('Footer main registers the pre footer image upload field')]
    public function testFooterMainRegistersThePreFooterImageField(): void
    {
        new FooterMain('municipio_customizer_section_component_footer');

        $preFooterImageField = $this->findFieldBySettings(PanelsRegistry::getInstance()->getRegisteredFields(), 'footer_pre_footer_image');

        $this->assertNotNull($preFooterImageField);
        $this->assertSame('upload', $preFooterImageField['type']);
        $this->assertSame('refresh', $preFooterImageField['transport']);
    }

    #[TestDox('Footer main registers the taller pre footer image on mobile switch, off by default')]
    public function testFooterMainRegistersTheTallerOnMobileSwitch(): void
    {
        new FooterMain('municipio_customizer_section_component_footer');

        $field = $this->findFieldBySettings(PanelsRegistry::getInstance()->getRegisteredFields(), 'footer_pre_footer_image_taller_on_mobile');

        $this->assertNotNull($field);
        $this->assertSame('switch', $field['type']);
        $this->assertFalse($field['default']);
    }

    #[TestDox('Footer main registers the mobile height slider, only active when taller on mobile is enabled')]
    public function testFooterMainRegistersTheMobileHeightSlider(): void
    {
        new FooterMain('municipio_customizer_section_component_footer');

        $field = $this->findFieldBySettings(PanelsRegistry::getInstance()->getRegisteredFields(), 'footer_pre_footer_image_mobile_height');

        $this->assertNotNull($field);
        $this->assertSame('slider', $field['type']);
        $this->assertSame(160, $field['default']);
        $this->assertSame(['min' => 100, 'max' => 400, 'step' => 10], $field['choices']);
        $this->assertSame('footer_pre_footer_image_taller_on_mobile', $field['active_callback'][0]['setting']);
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
