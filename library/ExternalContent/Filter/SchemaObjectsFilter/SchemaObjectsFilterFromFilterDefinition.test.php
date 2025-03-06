<?php

namespace Municipio\ExternalContent\Filter\SchemaObjectsFilter;

use Municipio\ExternalContent\Filter\FilterDefinition\Contracts\Enums\Operator;
use Municipio\ExternalContent\Filter\FilterDefinition\FilterDefinition;
use Municipio\ExternalContent\Filter\FilterDefinition\Rule;
use Municipio\ExternalContent\Filter\FilterDefinition\RuleSet;
use PHPUnit\Framework\TestCase;
use Spatie\SchemaOrg\Schema;

class SchemaObjectsFilterFromFilterDefinitionTest extends TestCase
{
    /**
     * @testdox class can be instantiated
     */
    public function testCanBeInstantiated()
    {
        $schemaObjectsFilterFromFilterDefinition = new SchemaObjectsFilterFromFilterDefinition(new FilterDefinition([]));
        $this->assertInstanceOf(SchemaObjectsFilterFromFilterDefinition::class, $schemaObjectsFilterFromFilterDefinition);
    }

    /**
     * @testdox the transform result can be used to filter an array of schema objects
     */
    public function testTransformResultCanBeUsedToFilterAnArrayOfSchemaObjects()
    {

        $schemaObjects       = [ Schema::thing()->name('Foo'), Schema::thing()->name('Bar'), Schema::thing()->name('Baz'), ];
        $filterDefinition    = new FilterDefinition([new RuleSet([new Rule('name', 'Foo', Operator::EQUALS)])]);
        $schemaObjectsFilter = new SchemaObjectsFilterFromFilterDefinition($filterDefinition);

        $this->assertEquals([Schema::thing()->name('Foo')], $schemaObjectsFilter->filter($schemaObjects));
    }
}
