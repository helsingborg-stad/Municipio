<?php

namespace Municipio\SchemaData\Taxonomy;

use Municipio\Schema\Schema;
use Municipio\SchemaData\Taxonomy\TaxonomiesFromSchemaType\TaxonomyInterface;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class TermFactoryTest extends TestCase
{
    private TermFactory $instance;

    protected function setUp(): void
    {
        $this->instance = new TermFactory();
    }

    #[TestDox('returns an array from property that contains a string')]
    public function testCreateReturnsArray(): void
    {
        $taxonomy = $this->getTaxonomy();
        $taxonomy->method('getSchemaProperty')->willReturn('physicalAccessibilityFeatures');
        $schema = Schema::event()->physicalAccessibilityFeatures('wheelchairAccessible')->toArray();

        $this->assertEquals('wheelchairAccessible', $this->instance->create($taxonomy, $schema)[0]->name);
    }

    #[TestDox('returns an array from property that contains an array of strings')]
    public function testCreateReturnsArrayOfStrings(): void
    {
        $taxonomy = $this->getTaxonomy();
        $taxonomy->method('getSchemaProperty')->willReturn('physicalAccessibilityFeatures');
        $schema = Schema::event()->physicalAccessibilityFeatures(['wheelchairAccessible', 'brailleSignage'])->toArray();

        $result = $this->instance->create($taxonomy, $schema);

        $this->assertEquals('wheelchairAccessible', $result[0]->name);
    }

    #[TestDox('returns name from property if target property contains a schema with name')]
    public function testCreateReturnsNameFromProperty(): void
    {
        $taxonomy = $this->getTaxonomy();
        $taxonomy->method('getSchemaProperty')->willReturn('actor');
        $schema = Schema::event()->actor(Schema::person()->name('John Doe'))->toArray();

        $result = $this->instance->create($taxonomy, $schema);

        $this->assertEquals('John Doe', $result[0]->name);
    }

    #[TestDox('returns a names from property if target property contains an array of schemas with names')]
    public function testCreateReturnsNamesFromProperty(): void
    {
        $taxonomy = $this->getTaxonomy();
        $taxonomy->method('getSchemaProperty')->willReturn('actor');
        $schema = Schema::event()->actor([ Schema::person()->name('John Doe'), Schema::person()->name('Jane Smith') ])->toArray();

        $result = $this->instance->create($taxonomy, $schema);

        $this->assertEquals('John Doe', $result[0]->name);
        $this->assertEquals('Jane Smith', $result[1]->name);
    }

    #[TestDox('returns an empty array if target property is not found')]
    public function testCreateReturnsEmptyArrayIfTargetPropertyNotFound(): void
    {
        $taxonomy = $this->getTaxonomy();
        $taxonomy->method('getSchemaProperty')->willReturn('nonExistentProperty');
        $schema = Schema::event()->toArray();

        $result = $this->instance->create($taxonomy, $schema);

        $this->assertEquals([], $result);
    }

    #[TestDox('converts values of float to string')]
    public function testCreateConvertsFloatToString(): void
    {
        $taxonomy = $this->getTaxonomy();
        $taxonomy->method('getSchemaProperty')->willReturn('maximumAttendeeCapacity');
        $schema = Schema::event()->maximumAttendeeCapacity(4.5)->toArray();

        $result = $this->instance->create($taxonomy, $schema);

        $this->assertEquals('4.5', $result[0]->name);
    }

    #[TestDox('converts values of int to string')]
    public function testCreateConvertsIntToString(): void
    {
        $taxonomy = $this->getTaxonomy();
        $taxonomy->method('getSchemaProperty')->willReturn('maximumAttendeeCapacity');
        $schema = Schema::event()->maximumAttendeeCapacity(4)->toArray();

        $result = $this->instance->create($taxonomy, $schema);

        $this->assertEquals('4', $result[0]->name);
    }

    #[TestDox('converts an array of int to an array of strings')]
    public function testCreateConvertsArrayOfIntToArrayOfStrings(): void
    {
        $taxonomy = $this->getTaxonomy();
        $taxonomy->method('getSchemaProperty')->willReturn('maximumAttendeeCapacity');
        $schema = Schema::event()->maximumAttendeeCapacity([4, 5])->toArray();

        $result = $this->instance->create($taxonomy, $schema);

        $this->assertEquals(['4', '5'], [$result[0]->name, $result[1]->name]);
    }

    #[TestDox('allows for nested properties inside arrays')]
    public function testCreateHandlesNestedPropertiesInArrays(): void
    {
        $taxonomy = $this->getTaxonomy();
        $taxonomy->method('getSchemaProperty')->willReturn('actor.callSign');
        $schema = Schema::event()->actor([Schema::person()->callSign('JDoe'), Schema::person()->callSign('JSmith')])->toArray();

        $result = $this->instance->create($taxonomy, $schema);

        $this->assertEquals(['JDoe', 'JSmith'], [$result[0]->name, $result[1]->name]);
    }

    #[TestDox('extracts values of nested array of PropertyValue objects')]
    public function testCreateExtractsValuesOfNestedArrayOfPropertyValueObjects(): void
    {
        $taxonomy = $this->getTaxonomy();
        $taxonomy->method('getSchemaProperty')->willReturn('@meta.category');
        $schema = Schema::thing()->setProperty('@meta', [
            Schema::propertyValue()->name('category')->value('TestCategory'),
            Schema::propertyValue()->name('tag')->value('TestTag'),
        ]);

        $result = $this->instance->create($taxonomy, $schema->toArray());

        $this->assertEquals('TestCategory', $result[0]->name);
    }

    #[TestDox('does not extract values of nested array of PropertyValue objects if name does not match target property')]
    public function testCreateDoesNotExtractValuesOfNestedArrayOfPropertyValueObjectsIfNameDoesNotMatchTargetProperty(): void
    {
        $taxonomy = $this->getTaxonomy();
        $taxonomy->method('getSchemaProperty')->willReturn('@meta.tag');
        $schema = Schema::thing()->setProperty('@meta', [
            Schema::propertyValue()->name('category')->value('TestCategory'),
        ]);

        $result = $this->instance->create($taxonomy, $schema->toArray());

        $this->assertEmpty($result);
    }

    private function getTaxonomy(): MockObject|TaxonomyInterface
    {
        $mock = $this->createMock(TaxonomyInterface::class);
        $mock->method('formatTermValue')->willReturnCallback(function ($value, $schema) {
            return $value;
        });

        return $mock;
    }
}
