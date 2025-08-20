<?php

namespace Municipio\SchemaData\ExternalContent\WpPostArgsFromSchemaObject;

use PHPUnit\Framework\TestCase;
use Municipio\Schema\BaseType;
use WpService\Contracts\GetPosts;
use WpService\Implementations\FakeWpService;

class IdDecoratorTest extends TestCase
{
    /**
     * @testdox class can be instantiated
     */
    public function testClassCanBeInstantiated()
    {
        $factory = new IdDecorator('', '', new WpPostArgsFromSchemaObject(), new FakeWpService());
        $this->assertInstanceOf(IdDecorator::class, $factory);
    }
}
