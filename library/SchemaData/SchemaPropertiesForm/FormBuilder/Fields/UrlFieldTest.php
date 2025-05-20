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
        $this->assertEquals('website', $result['name']);
        $this->assertEquals('Website', $result['label']);
        $this->assertEquals('https://example.com', $urlField->getValue());
    }

    public function testToArraySanitizesInvalidUrl()
    {
        $urlField = new UrlField('website', 'Website', 'invalid-url');

        $this->assertEquals('', $urlField->getValue());
    }

    public function testToArrayHandlesNullValue()
    {
        $urlField = new UrlField('website', 'Website', null);

        $this->assertEquals('', $urlField->getValue());
    }

    public function testToArrayHandlesEmptyStringValue()
    {
        $urlField = new UrlField('website', 'Website', '');

        $this->assertEquals('', $urlField->getValue());
    }
}
