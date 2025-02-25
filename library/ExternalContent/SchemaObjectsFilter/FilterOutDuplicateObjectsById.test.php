<?php

namespace Municipio\ExternalContent\SchemaObjectsFilter;

use PHPUnit\Framework\TestCase;

class FilterOutDuplicateObjectsByIdTest extends TestCase
{
    /**
     * @testdox class can be instantiated
     */
    public function testCanBeInstantiated()
    {
        $filterOutDuplicateObjectsById = new FilterOutDuplicateObjectsById();
        $this->assertInstanceOf(FilterOutDuplicateObjectsById::class, $filterOutDuplicateObjectsById);
    }

    /**
     * @testdox applyFilter returns schema objects without duplicates by id
     */
    public function testApplyFilterReturnsSchemaObjectsWithoutDuplicatesById()
    {
        $schemaObjects = [
            \Spatie\SchemaOrg\Schema::person()->setProperty('@id', '1'),
            \Spatie\SchemaOrg\Schema::person()->setProperty('@id', '2'),
            \Spatie\SchemaOrg\Schema::person()->setProperty('@id', '1'),
        ];

        $filter                = new FilterOutDuplicateObjectsById();
        $filteredSchemaObjects = $filter->applyFilter($schemaObjects);

        $this->assertCount(2, $filteredSchemaObjects);
    }
}
