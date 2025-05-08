<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields;

use PHPUnit\Framework\TestCase;
use Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields\UrlField;

class UrlFieldTest extends TestCase
{
    public function testToArrayReturnsCorrectStructure()
    {
        $urlField = new UrlField('website', 'Website', 'https://example.com');
        $result   = $urlField->toArray();

        $this->assertIsArray($result);
        $this->assertEquals('url', $result['type']);
        $this->assertEquals('website', $result['key']);
        $this->assertEquals('website', $result['name']);
        $this->assertEquals('Website', $result['label']);
        $this->assertEquals('https://example.com', $result['value']);
    }

    public function testToArraySanitizesInvalidUrl()
    {
        $urlField = new UrlField('website', 'Website', 'invalid-url');
        $result   = $urlField->toArray();

        $this->assertEquals('', $result['value']);
    }

    public function testToArrayHandlesNullValue()
    {
        $urlField = new UrlField('website', 'Website', null);
        $result   = $urlField->toArray();

        $this->assertEquals('', $result['value']);
    }

    public function testToArrayHandlesEmptyStringValue()
    {
        $urlField = new UrlField('website', 'Website', '');
        $result   = $urlField->toArray();

        $this->assertEquals('', $result['value']);
    }
}
