<?php

namespace Municipio\ExternalContent\Config;

use PHPUnit\Framework\TestCase;
use Municipio\ExternalContent\Config\SourceTaxonomyConfig;

class SourceTaxonomyConfigTest extends TestCase
{
    private SourceTaxonomyConfig $config;

    protected function setUp(): void
    {
        $this->config = new SourceTaxonomyConfig(
            'schemaObjectType',
            'some.schemaProperty',
            'Plural Name',
            'Singular Name',
            true
        );
    }

    public function testGetFromSchemaProperty(): void
    {
        $this->assertEquals('some.schemaProperty', $this->config->getFromSchemaProperty());
    }

    public function testGetPluralName(): void
    {
        $this->assertEquals('Plural Name', $this->config->getPluralName());
    }

    public function testGetSingularName(): void
    {
        $this->assertEquals('Singular Name', $this->config->getSingularName());
    }

    public function testIsHierarchical(): void
    {
        $this->assertTrue($this->config->isHierarchical());
    }

    public function testGetName(): void
    {
        $this->assertEquals('schema_object_type_some_schema_p', $this->config->getName());
    }
}
