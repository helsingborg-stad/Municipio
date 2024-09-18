<?php

namespace Municipio\ExternalContent\WpPostArgsFromSchemaObject;

use Municipio\ExternalContent\Sources\Source;
use PHPUnit\Framework\TestCase;
use Spatie\SchemaOrg\Thing;

class VersionDecoratorTest extends TestCase
{
    public function testAppliesVersionIfFound()
    {
        $schemaObject = new Thing();
        $schemaObject->setProperty('@version', '1.2.3');
        $factory = new VersionDecorator(new WpPostFactory());

        $result = $factory->create($schemaObject, new Source('', ''));

        $this->assertEquals('1.2.3', $result['meta_input']['version']);
    }

    public function testAppliesNothingIfNotFound()
    {
        $schemaObject = new Thing();
        $factory      = new VersionDecorator(new WpPostFactory());

        $result = $factory->create($schemaObject, new Source('', ''));

        $this->assertArrayNotHasKey('version', $result);
    }
}
