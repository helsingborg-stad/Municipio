<?php

declare(strict_types=1);

namespace Municipio\Customizer\Sections;

use Municipio\Customizer\PanelsRegistry;
use PHPUnit\Framework\TestCase;

class LoadDesignTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        PanelsRegistry::getInstance()->fields = [];
    }

    public function testRegistersOnlyUrlFieldWithInstructions(): void
    {
        $sectionId = 'municipio_customizer_section_import_design';

        new LoadDesign($sectionId);

        $fields = PanelsRegistry::getInstance()->getRegisteredFields();
        $this->assertCount(1, $fields);

        $field = $fields[0];

        $this->assertSame(LoadDesign::LOAD_DESIGN_SITE_URL_KEY, $field['settings']);
        $this->assertSame('url', $field['type']);
        $this->assertSame($sectionId, $field['section']);
        $this->assertArrayHasKey('description', $field);
        $this->assertArrayHasKey('input_attrs', $field);
        $this->assertSame('https://example.se', $field['input_attrs']['placeholder']);
        $this->assertSame('postMessage', $field['transport']);
    }
}
