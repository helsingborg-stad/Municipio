<?php

namespace Municipio\Customizer\Sections\Component;

use Municipio\Customizer\PanelsRegistry;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class FooterSubTest extends TestCase
{
    protected function setUp(): void
    {
        PanelsRegistry::getInstance()->fields = [];
    }

    #[TestDox('Footer sub content repeater refreshes the preview when component data changes')]
    public function testFooterSubContentRepeaterRefreshesPreviewWhenComponentDataChanges(): void
    {
        new FooterSub('municipio_customizer_section_component_footer');

        $fields = PanelsRegistry::getInstance()->getRegisteredFields();
        $logotypeField = $this->getFieldBySettings($fields, 'footer_subfooter_logotype');
        $contentField = $this->getFieldBySettings($fields, 'footer_subfooter_content');
        $customLogotypeField = $this->getFieldBySettings($fields, 'footer_subfooter_custom_logotype');

        $this->assertSame('select', $logotypeField['type']);
        $this->assertSame('upload', $customLogotypeField['type']);
        $this->assertSame('repeater', $contentField['type']);
        $this->assertArrayHasKey('title', $contentField['fields']);
        $this->assertArrayHasKey('content', $contentField['fields']);
        $this->assertArrayHasKey('link', $contentField['fields']);
        $this->assertSame('refresh', $contentField['transport']);
        $this->assertSame('component_data', $contentField['output'][0]['type']);
        $this->assertSame('subfooter.content', $contentField['output'][0]['dataKey']);
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
