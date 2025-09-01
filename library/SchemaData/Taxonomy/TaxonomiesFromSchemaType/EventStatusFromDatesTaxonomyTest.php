<?php

namespace Municipio\SchemaData\Taxonomy\TaxonomiesFromSchemaType;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\_x;

class EventStatusFromDatesTaxonomyTest extends TestCase
{
    /**
     * @testdox class can be instantiated
     */
    public function testClassCanBeInstantiated(): void
    {
        $this->assertInstanceOf(EventStatusFromDatesTaxonomy::class, $this->getInstance());
    }

    /**
     * @testdox formatTermValue() returns empty string if either startDate or endDate is invalid
     */
    public function testFormatTermValueReturnsEmptyStringIfDatesAreInvalid(): void
    {
        $taxonomy = $this->getInstance();

        $this->assertSame('', $taxonomy->formatTermValue('', []));
        $this->assertSame('', $taxonomy->formatTermValue('', ['startDate' => '2025-08-01']));
        $this->assertSame('', $taxonomy->formatTermValue('', ['endDate' => '2025-08-01']));
    }

    /**
     * @testdox formatTermValue() returns "Ongoing" if startDate is in the past and endDate is in the future
     */
    public function testFormatTermValueReturnsOngoingIfStartDateIsInThePastAndEndDateIsInTheFuture(): void
    {
        $taxonomy  = $this->getInstance();
        $startDate = date('Y-m-d', strtotime('-1 days'));
        $endDate   = date('Y-m-d', strtotime('+1 days'));

        $this->assertSame('Ongoing', $taxonomy->formatTermValue('2025-08-01', ['startDate' => $startDate, 'endDate' => $endDate]));
    }

    /**
     * @testdox formatTermValue() returns "Closed" if both dates are in the past
     */
    public function testFormatTermValueReturnsClosedIfBothDatesAreInThePast(): void
    {
        $taxonomy  = $this->getInstance();
        $startDate = date('Y-m-d', strtotime('-11 days'));
        $endDate   = date('Y-m-d', strtotime('-10 days'));

        $this->assertSame('Closed', $taxonomy->formatTermValue('2025-08-01', ['startDate' => $startDate, 'endDate' => $endDate]));
    }

    /**
     * @testdox formatTermValue() returns "Planned" if both dates are in the future
     */
    public function testFormatTermValueReturnsPlannedIfBothDatesAreInTheFuture(): void
    {
        $taxonomy  = $this->getInstance();
        $startDate = date('Y-m-d', strtotime('+10 days'));
        $endDate   = date('Y-m-d', strtotime('+11 days'));

        $this->assertSame('Planned', $taxonomy->formatTermValue('2025-08-01', ['startDate' => $startDate, 'endDate' => $endDate]));
    }

    private function getInstance(): EventStatusFromDatesTaxonomy
    {
        return new EventStatusFromDatesTaxonomy($this->getWpService(), $this->getInnerTaxonomy());
    }

    private function getWpService(): _x|MockObject
    {
        $mock = $this->createMock(_x::class);
        $mock->method('_x')->willReturnArgument(0);
        return $mock;
    }

    private function getInnerTaxonomy(): TaxonomyInterface|MockObject
    {
        $mock = $this->createMock(TaxonomyInterface::class);
        $mock->method('getName')->willReturn('event_status');
        $mock->method('getSchemaType')->willReturn('Event');
        $mock->method('getSchemaProperty')->willReturn('startDate');
        $mock->method('getObjectTypes')->willReturn(['event']);
        $mock->method('getArguments')->willReturn([]);
        return $mock;
    }
}
