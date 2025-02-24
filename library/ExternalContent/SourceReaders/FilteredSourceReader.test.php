<?php

namespace Municipio\ExternalContent\SourceReaders;

use Municipio\ExternalContent\SchemaObjectsFilter\SchemaObjectsFilterInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Spatie\SchemaOrg\Schema;

class FilteredSourceReaderTest extends TestCase
{
    /**
     * @testdox class can be instantiated
     */
    public function testCanBeInstantiated()
    {
        $filteredSourceReader = new FilteredSourceReader($this->getInnerReader(), $this->getFilter());
        $this->assertInstanceOf(FilteredSourceReader::class, $filteredSourceReader);
    }

    /**
     * @testdox getSourceData returns result from schemaObjectsFilter
     */
    public function testGetSourceDataReturnsResultFromSchemaObjectsFilter()
    {
        $innerReader   = $this->getInnerReader();
        $schemaObjects = [Schema::person()->name('foo'), Schema::person()->name('bar')];
        $innerReader->expects($this->once())->method('getSourceData')->willReturn($schemaObjects);

        $filter = $this->getFilter();
        $filter->method('applyFilter')->with($schemaObjects)->willReturn([$schemaObjects[0]]);

        $filteredSourceReader = new FilteredSourceReader($innerReader, $filter);
        $this->assertEquals([$schemaObjects[0]], $filteredSourceReader->getSourceData());
    }

    private function getInnerReader(): SourceReaderInterface|MockObject
    {
        return $this->createMock(SourceReaderInterface::class);
    }

    private function getFilter(): SchemaObjectsFilterInterface|MockObject
    {
        return $this->createMock(SchemaObjectsFilterInterface::class);
    }
}
