<?php

namespace Municipio\SchemaData\ExternalContent\Filter\SchemaObjectsFilter;

use Municipio\Schema\Schema;
use Municipio\SchemaData\ExternalContent\Filter\FilterDefinition\Contracts\Enums\Operator;
use Municipio\SchemaData\ExternalContent\Filter\FilterDefinition\Contracts\Enums\Relation;
use Municipio\SchemaData\ExternalContent\Filter\FilterDefinition\FilterDefinition;
use Municipio\SchemaData\ExternalContent\Filter\FilterDefinition\Rule;
use Municipio\SchemaData\ExternalContent\Filter\FilterDefinition\RuleSet;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class SchemaObjectsFilterFromFilterDefinitionTest extends TestCase
{
    #[TestDox('class can be instantiated')]
    public function testCanBeInstantiated()
    {
        $schemaObjectsFilterFromFilterDefinition = new SchemaObjectsFilterFromFilterDefinition(new FilterDefinition([]));
        $this->assertInstanceOf(SchemaObjectsFilterFromFilterDefinition::class, $schemaObjectsFilterFromFilterDefinition);
    }

    #[TestDox('the transform result can be used to filter an array of schema objects')]
    public function testTransformResultCanBeUsedToFilterAnArrayOfSchemaObjects()
    {
        $schemaObjects = [Schema::thing()->name('Foo'), Schema::thing()->name('Bar'), Schema::thing()->name('Baz')];
        $filterDefinition = new FilterDefinition([new RuleSet([new Rule('name', 'Foo', Operator::EQUALS)])]);
        $schemaObjectsFilter = new SchemaObjectsFilterFromFilterDefinition($filterDefinition);

        $this->assertEquals([Schema::thing()->name('Foo')], $schemaObjectsFilter->filter($schemaObjects));
    }

    #[TestDox('filter definition with multiple rules in a ruleset respects the provided ruleset operator (OR)')]
    public function testDefaultFilterBehaviorIsToUseDisjunctionBetweenRulesets()
    {
        $schemaObjects = [
            Schema::thing()->name('Foo')->description('First'),
            Schema::thing()->name('Bar')->description('Second'),
            Schema::thing()->name('Baz')->description('Third'),
        ];
        $filterDefinition = new FilterDefinition([
            new RuleSet([new Rule('name', 'Foo', Operator::EQUALS), new Rule('description', 'Third', Operator::EQUALS)], Relation::OR),
        ]);
        $schemaObjectsFilter = new SchemaObjectsFilterFromFilterDefinition($filterDefinition);

        $this->assertEquals(
            [
                Schema::thing()->name('Foo')->description('First'),
                Schema::thing()->name('Baz')->description('Third'),
            ],
            $schemaObjectsFilter->filter($schemaObjects),
        );
    }

    #[TestDox('filter definition with multiple rules in a ruleset respects the provided ruleset operator (AND)')]
    public function testFilterBehaviorWithMultipleRulesInARulesetIsConjunction()
    {
        $schemaObjects = [
            Schema::thing()->name('Foo')->description('First'),
            Schema::thing()->name('Foo')->description('Second'),
            Schema::thing()->name('Baz')->description('Third'),
        ];
        $filterDefinition = new FilterDefinition([
            new RuleSet([
                new Rule('name', 'Foo', Operator::EQUALS),
                new Rule('description', 'Second', Operator::EQUALS),
            ], Relation::AND),
        ]);
        $schemaObjectsFilter = new SchemaObjectsFilterFromFilterDefinition($filterDefinition);

        $this->assertEquals(
            [
                Schema::thing()->name('Foo')->description('Second'),
            ],
            $schemaObjectsFilter->filter($schemaObjects),
        );
    }
}
