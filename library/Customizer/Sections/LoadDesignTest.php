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

    public function testRegistersUrlFieldAndExplicitImportButton(): void
    {
        $sectionId = 'municipio_customizer_section_import_design';

        new LoadDesign($sectionId);

        $fields = PanelsRegistry::getInstance()->getRegisteredFields();
        $this->assertCount(2, $fields);

        $urlField = $fields[0];
        $buttonField = $fields[1];

        $this->assertSame(LoadDesign::LOAD_DESIGN_SITE_URL_KEY, $urlField['settings']);
        $this->assertSame('url', $urlField['type']);
        $this->assertSame($sectionId, $urlField['section']);
        $this->assertArrayHasKey('description', $urlField);
        $this->assertArrayHasKey('input_attrs', $urlField);
        $this->assertSame('https://example.se', $urlField['input_attrs']['placeholder']);
        $this->assertSame('postMessage', $urlField['transport']);

        $this->assertSame(LoadDesign::LOAD_DESIGN_IMPORT_ACTION_KEY, $buttonField['settings']);
        $this->assertSame('custom', $buttonField['type']);
        $this->assertSame($sectionId, $buttonField['section']);
        $this->assertStringContainsString('municipio-design-import-button', $buttonField['default']);
    }
}
