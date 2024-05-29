<?php

namespace Municipio\ExternalContent\WpPostMetaFactory;

use Municipio\ExternalContent\Sources\Services\NullSourceService;
use Municipio\ExternalContent\Sources\Services\Source;
use PHPUnit\Framework\TestCase;
use Spatie\SchemaOrg\Thing;

class WpPostMetaFactoryVersionDecoratorTest extends TestCase
{
    public function testAppliesVersionIfFound()
    {
        $schemaObject = new Thing();
        $schemaObject->setProperty('@version', '1.2.3');
        $factory = new WpPostMetaFactoryVersionDecorator(new WpPostMetaFactory());

        $result = $factory->create($schemaObject, new Source('', ''));

        $this->assertEquals('1.2.3', $result['version']);
    }

    public function testAppliesNothingIfNotFound()
    {
        $schemaObject = new Thing();
        $factory      = new WpPostMetaFactoryVersionDecorator(new WpPostMetaFactory());

        $result = $factory->create($schemaObject, new Source('', ''));

        $this->assertArrayNotHasKey('version', $result);
    }
}
