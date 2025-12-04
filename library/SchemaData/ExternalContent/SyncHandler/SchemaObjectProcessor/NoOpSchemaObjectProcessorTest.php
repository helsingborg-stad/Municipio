<?php

declare(strict_types=1);

namespace Municipio\SchemaData\ExternalContent\SyncHandler\SchemaObjectProcessor;

use PHPUnit\Framework\TestCase;
use Municipio\Schema\BaseType;
use PHPUnit\Framework\MockObject\MockObject;

class NoOpSchemaObjectProcessorTest extends TestCase
{
    public function testImplementsInterface(): void
    {
        $processor = new NoOpSchemaObjectProcessor();
        $this->assertInstanceOf(SchemaObjectProcessorInterface::class, $processor);
    }

    public function testProcessReturnsSameObject(): void
    {
        $processor        = new NoOpSchemaObjectProcessor();
        $mockSchemaObject = $this->getSchemaObject();
        $result           = $processor->process($mockSchemaObject);
        $this->assertSame($mockSchemaObject, $result);
    }

    private function getSchemaObject(): BaseType|MockObject
    {
        return $this->getMockBuilder(BaseType::class)->getMock();
    }
}
