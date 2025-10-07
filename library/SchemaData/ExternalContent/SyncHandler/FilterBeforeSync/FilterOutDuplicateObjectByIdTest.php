<?php

namespace Municipio\SchemaData\ExternalContent\SyncHandler\FilterBeforeSync;

use PHPUnit\Framework\TestCase;
use Municipio\Schema\Schema;

class FilterOutDuplicateObjectByIdTest extends TestCase
{
    /**
     * @testdox class can be instantiated
     */
    public function testClassCanBeInstantiated()
    {
        $filter = new FilterOutDuplicateObjectById();
        $this->assertInstanceOf(FilterOutDuplicateObjectById::class, $filter);
    }

    /**
     * @testdox filters out duplicate objects from a collection based on their unique identifier
     */
    public function testFiltersOutDuplicateObjectsFromCollectionBasedOnUniqueIdentifier()
    {
        $filter        = new FilterOutDuplicateObjectById();
        $schemaObjects = [
            Schema::thing()->setProperty('@id', '1'),
            Schema::thing()->setProperty('@id', '2'),
            Schema::thing()->setProperty('@id', '1'),
            Schema::thing()->setProperty('@id', '3'),
            Schema::thing()->setProperty('@id', '2'),
        ];

        $filteredSchemaObjects = $filter->filter($schemaObjects);

        // reset keys to make it easier to compare
        $filteredSchemaObjects = array_values($filteredSchemaObjects);

        $this->assertCount(3, $filteredSchemaObjects);
        $this->assertSame('1', $filteredSchemaObjects[0]->getProperty('@id'));
        $this->assertSame('2', $filteredSchemaObjects[1]->getProperty('@id'));
        $this->assertSame('3', $filteredSchemaObjects[2]->getProperty('@id'));
    }
}
