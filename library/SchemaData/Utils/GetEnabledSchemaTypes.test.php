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
}
