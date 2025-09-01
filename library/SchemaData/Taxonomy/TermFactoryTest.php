<?php

namespace Municipio\SchemaData\Taxonomy;

use Municipio\Schema\Schema;
use Municipio\SchemaData\Taxonomy\TaxonomiesFromSchemaType\TaxonomyInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class TermFactoryTest extends TestCase
{
    private TermFactory $instance;

    protected function setUp(): void
    {
        $this->instance = new TermFactory();
    }

    /**
     * @testdox returns an array from property that contains a string
     */
    public function testCreateReturnsArray(): void
    {
        $taxonomy = $this->getTaxonomy();
        $taxonomy->method('getSchemaProperty')->willReturn('physicalAccessibilityFeatures');
        $schema = Schema::event()->physicalAccessibilityFeatures('wheelchairAccessible')->toArray();

        $this->assertEquals('wheelchairAccessible', $this->instance->create($taxonomy, $schema)[0]->name);
    }

    /**
     * @testdox returns an array from property that contains an array of strings
     */
    public function testCreateReturnsArrayOfStrings(): void
    {
        $taxonomy = $this->getTaxonomy();
        $taxonomy->method('getSchemaProperty')->willReturn('physicalAccessibilityFeatures');
        $schema = Schema::event()->physicalAccessibilityFeatures(['wheelchairAccessible', 'brailleSignage'])->toArray();

        $result = $this->instance->create($taxonomy, $schema);

        $this->assertEquals('wheelchairAccessible', $result[0]->name);
    }

    /**
     * @testdox returns name from property if target property contains a schema with name
     */
    public function testCreateReturnsNameFromProperty(): void
    {
        $taxonomy = $this->getTaxonomy();
        $taxonomy->method('getSchemaProperty')->willReturn('actor');
        $schema = Schema::event()->actor(Schema::person()->name('John Doe'))->toArray();

        $result = $this->instance->create($taxonomy, $schema);

        $this->assertEquals('John Doe', $result[0]->name);
    }

    /**
     * @testdox returns a names from property if target property contains an array of schemas with names
     */
    public function testCreateReturnsNamesFromProperty(): void
    {
        $taxonomy = $this->getTaxonomy();
        $taxonomy->method('getSchemaProperty')->willReturn('actor');
        $schema = Schema::event()->actor([ Schema::person()->name('John Doe'), Schema::person()->name('Jane Smith') ])->toArray();

        $result = $this->instance->create($taxonomy, $schema);

        $this->assertEquals('John Doe', $result[0]->name);
        $this->assertEquals('Jane Smith', $result[1]->name);
    }

    /**
     * @testdox returns an empty array if target property is not found
     */
    public function testCreateReturnsEmptyArrayIfTargetPropertyNotFound(): void
    {
        $taxonomy = $this->getTaxonomy();
        $taxonomy->method('getSchemaProperty')->willReturn('nonExistentProperty');
        $schema = Schema::event()->toArray();

        $result = $this->instance->create($taxonomy, $schema);

        $this->assertEquals([], $result);
    }

    /**
     * @testdox converts values of float to string
     */
    public function testCreateConvertsFloatToString(): void
    {
        $taxonomy = $this->getTaxonomy();
        $taxonomy->method('getSchemaProperty')->willReturn('maximumAttendeeCapacity');
        $schema = Schema::event()->maximumAttendeeCapacity(4.5)->toArray();

        $result = $this->instance->create($taxonomy, $schema);

        $this->assertEquals('4.5', $result[0]->name);
    }

    /**
     * @testdox converts values of int to string
     */
    public function testCreateConvertsIntToString(): void
    {
        $taxonomy = $this->getTaxonomy();
        $taxonomy->method('getSchemaProperty')->willReturn('maximumAttendeeCapacity');
        $schema = Schema::event()->maximumAttendeeCapacity(4)->toArray();

        $result = $this->instance->create($taxonomy, $schema);

        $this->assertEquals('4', $result[0]->name);
    }

    /**
     * @testdox converts an array of int to an array of strings
     */
    public function testCreateConvertsArrayOfIntToArrayOfStrings(): void
    {
        $taxonomy = $this->getTaxonomy();
        $taxonomy->method('getSchemaProperty')->willReturn('maximumAttendeeCapacity');
        $schema = Schema::event()->maximumAttendeeCapacity([4, 5])->toArray();

        $result = $this->instance->create($taxonomy, $schema);

        $this->assertEquals(['4', '5'], [$result[0]->name, $result[1]->name]);
    }

    /**
     * @testdox allows for nested properties inside arrays
     */
    public function testCreateHandlesNestedPropertiesInArrays(): void
    {
        $taxonomy = $this->getTaxonomy();
        $taxonomy->method('getSchemaProperty')->willReturn('actor.callSign');
        $schema = Schema::event()->actor([Schema::person()->callSign('JDoe'), Schema::person()->callSign('JSmith')])->toArray();

        $result = $this->instance->create($taxonomy, $schema);

        $this->assertEquals(['JDoe', 'JSmith'], [$result[0]->name, $result[1]->name]);
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
