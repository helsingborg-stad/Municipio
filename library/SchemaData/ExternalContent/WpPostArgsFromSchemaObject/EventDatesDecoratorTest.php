<?php

namespace Municipio\SchemaData\ExternalContent\WpPostArgsFromSchemaObject;

use Municipio\SchemaData\ExternalContent\WpPostArgsFromSchemaObject\WpPostArgsFromSchemaObjectInterface;
use PHPUnit\Framework\TestCase;
use Municipio\Schema\BaseType;
use Municipio\Schema\Schema;
use PHPUnit\Framework\MockObject\MockObject;

class EventDatesDecoratorTest extends TestCase
{
    #[TestDox('class can be instantiated')]
    public function testCanBeInstantiated()
    {
        $decorator = new EventDatesDecorator($this->getInnerMock());
        $this->assertInstanceOf(EventDatesDecorator::class, $decorator);
    }

    #[TestDox('returns the same post args as the inner decorator if the schema object is not an event')]
    public function testReturnsSamePostArgsIfNotEvent()
    {
        $mockInner = $this->getInnerMock();
        $mockInner->method('transform')->willReturn(['meta_input' => []]);

        $decorator = new EventDatesDecorator($mockInner);

        $schemaObject = $this->getSchemaMock();
        $schemaObject->method('getType')->willReturn('Article');

        $result = $decorator->transform($schemaObject);

        $this->assertEquals(['meta_input' => []], $result);
    }

    #[TestDox('adds startDate and endDate to meta_input if the schema object is an event')]
    public function testAddsStartDateAndEndDateIfEvent()
    {
        $mockInner = $this->getInnerMock();
        $mockInner->method('transform')->willReturn(['meta_input' => []]);

        $decorator = new EventDatesDecorator($mockInner);

        $schemaObject = Schema::exhibitionEvent()->startDate('2021-01-01')->endDate('2021-01-02');

        $result = $decorator->transform($schemaObject);

        $this->assertEquals(['meta_input' => ['startDate' => '2021-01-01', 'endDate' => '2021-01-02']], $result);
    }

    #[TestDox('accepts subtypes of Event')]
    public function testAcceptsSubtypesOfEvent()
    {
        $mockInner = $this->getInnerMock();
        $mockInner->method('transform')->willReturn(['meta_input' => []]);

        $decorator = new EventDatesDecorator($mockInner);

        $schemaObject = Schema::event()->startDate('2021-01-01')->endDate('2021-01-02');

        $result = $decorator->transform($schemaObject);

        $this->assertEquals(['meta_input' => ['startDate' => '2021-01-01', 'endDate' => '2021-01-02']], $result);
    }

    private function getInnerMock(): WpPostArgsFromSchemaObjectInterface|MockObject
    {
        return $this->createMock(WpPostArgsFromSchemaObjectInterface::class);
    }

    private function getSchemaMock(): BaseType|MockObject
    {
        return $this->createMock(BaseType::class);
    }
}
