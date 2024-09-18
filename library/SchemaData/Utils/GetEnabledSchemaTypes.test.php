<?php

namespace Municipio\SchemaData\Utils;

use PHPUnit\Framework\TestCase;

class GetEnabledSchemaTypesTest extends TestCase
{
    private function getEnabledSchemaTypes(): array
    {
        $getEnabledSchemaTypes = new GetEnabledSchemaTypes();
        return $getEnabledSchemaTypes->getEnabledSchemaTypesAndProperties();
    }

    public function testContainsPlace()
    {
        $enabledSchemaTypes = $this->getEnabledSchemaTypes();
        $this->assertArrayHasKey('Place', $enabledSchemaTypes);
    }

    public function testContainsSchool()
    {
        $enabledSchemaTypes = $this->getEnabledSchemaTypes();
        $this->assertArrayHasKey('School', $enabledSchemaTypes);
    }

    public function testPlaceProperties()
    {
        $enabledSchemaTypes = $this->getEnabledSchemaTypes();
        $this->assertContains('geo', $enabledSchemaTypes['Place'], 'geo property is missing');
        $this->assertContains('telephone', $enabledSchemaTypes['Place'], 'telephone property is missing');
        $this->assertContains('url', $enabledSchemaTypes['Place'], 'url property is missing');
    }

    public function testContainsProject()
    {
        $this->assertArrayHasKey('Project', $this->getEnabledSchemaTypes());
    }

    public function testProjectProperties()
    {
        $enabledSchemaTypes = $this->getEnabledSchemaTypes();
        $this->assertContains('@id', $enabledSchemaTypes['Project']);
        $this->assertContains('description', $enabledSchemaTypes['Project']);
        $this->assertContains('name', $enabledSchemaTypes['Project']);
        $this->assertContains('department', $enabledSchemaTypes['Project']);
        $this->assertContains('employee', $enabledSchemaTypes['Project']);
        $this->assertContains('funding', $enabledSchemaTypes['Project']);
    }
}
