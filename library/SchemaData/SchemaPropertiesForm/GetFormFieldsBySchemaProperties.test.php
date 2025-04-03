<?php

namespace Municipio\SchemaData\SchemaPropertiesForm;

use Municipio\SchemaData\SchemaPropertiesForm\FormFieldFromSchemaProperty\FormFieldFromSchemaProperty;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\ApplyFilters;
use WpService\Implementations\FakeWpService;

class GetFormFieldsBySchemaPropertiesTest extends TestCase
{
    /**
     * @testdox applies filter Municipio/SchemaData/SchemaProperties to schema properties
     */
    public function testAppliesFilter()
    {
        $wpService = new FakeWpService(['applyFilters' => fn($hookName, $schemaProperties) => $schemaProperties]);
        $sut       = new GetFormFieldsBySchemaProperties($wpService);
        $sut->getFormFieldsBySchemaProperties('schemaType', []);

        $this->assertEquals('Municipio/SchemaData/SchemaProperties', $wpService->methodCalls['applyFilters'][0][0]);
    }
}
