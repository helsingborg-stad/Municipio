<?php

namespace Municipio\SchemaData\ExternalContent\WpPostArgsFromSchemaObject;

use Municipio\SchemaData\ExternalContent\WpPostArgsFromSchemaObject\WpPostArgsFromSchemaObjectInterface;
use PHPUnit\Framework\TestCase;
use Municipio\Schema\BaseType;
use Municipio\Schema\Schema;

class EventDatesDecoratorTest extends TestCase
{
    /**
     * @testdox class can be instantiated
     */
    public function testCanBeInstantiated()
    {
        $decorator = new EventDatesDecorator($this->createMock(WpPostArgsFromSchemaObjectInterface::class));
        $this->assertInstanceOf(EventDatesDecorator::class, $decorator);
    }

    /**
     * @testdox returns the same post args as the inner decorator if the schema object is not an event
     */
    public function testReturnsSamePostArgsIfNotEvent()
    {
        $mockInner = $this->createMock(WpPostArgsFromSchemaObjectInterface::class);
        $mockInner->method('transform')->willReturn(['meta_input' => []]);

        $decorator = new EventDatesDecorator($mockInner);

        $schemaObject = $this->createMock(BaseType::class);
        $schemaObject->method('getType')->willReturn('Article');

        $result = $decorator->transform($schemaObject);

        $this->assertEquals(['meta_input' => []], $result);
    }

    /**
     * @testdox adds startDate and endDate to meta_input if the schema object is an event
     */
    public function testAddsStartDateAndEndDateIfEvent()
    {
        $mockInner = $this->createMock(WpPostArgsFromSchemaObjectInterface::class);
        $mockInner->method('transform')->willReturn(['meta_input' => []]);

        $decorator = new EventDatesDecorator($mockInner);

        $schemaObject = Schema::event()->startDate('2021-01-01')->endDate('2021-01-02');

        $result = $decorator->transform($schemaObject);

        $this->assertEquals(['meta_input' => ['startDate' => '2021-01-01', 'endDate' => '2021-01-02']], $result);
    }
}
