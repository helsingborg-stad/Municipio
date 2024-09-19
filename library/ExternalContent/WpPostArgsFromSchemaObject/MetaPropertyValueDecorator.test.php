<?php

namespace Municipio\ExternalContent\WpPostArgsFromSchemaObject;

use Municipio\ExternalContent\Sources\Source;
use PHPUnit\Framework\TestCase;
use Spatie\SchemaOrg\BaseType;
use Spatie\SchemaOrg\Schema;

class MetaPropertyValueDecoratorTest extends TestCase
{
    /**
     * @testdox PropertyValue in @meta property gets added to post meta.
     */
    public function testCreate()
    {
        $schemaObject = $this->getSchemaObject();
        $schemaObject->setProperty('@meta', [Schema::propertyValue()->name('foo')->value('bar')]);
        $factory = new MetaPropertyValueDecorator();

        $postArgs = $factory->create($schemaObject, new Source('', ''));

        $this->assertEquals('bar', $postArgs['meta_input']['foo']);
    }

    /**
     * @testdox PropertyValue in @meta property with empty name gets ignored.
     */
    public function testCreateEmptyName()
    {
        $schemaObject = $this->getSchemaObject();
        $schemaObject->setProperty('@meta', [
            Schema::propertyValue()->name('')->value('bar'),
            Schema::propertyValue()->name('foo')->value(''),
        ]);
        $factory = new MetaPropertyValueDecorator();

        $postArgs = $factory->create($schemaObject, new Source('', ''));

        $this->assertEmpty($postArgs['meta_input']);
    }

    /**
     * @testdox PropertyValue in @meta property with value other than PropertyValue gets ignored.
     */
    public function testCreateNonPropertyValue()
    {
        $schemaObject = $this->getSchemaObject();
        $schemaObject->setProperty('@meta', [
            'foo',
            Schema::jobPosting()->title('Job title'),
        ]);

        $factory  = new MetaPropertyValueDecorator();
        $postArgs = $factory->create($schemaObject, new Source('', ''));

        $this->assertEmpty($postArgs['meta_input']);
    }

    private function getSchemaObject(): BaseType
    {
        return new class extends BaseType {
        };
    }
}
