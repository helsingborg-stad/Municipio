<?php

namespace Municipio\Controller\SingularEvent\Mappers;

use Municipio\Schema\Organization;
use Municipio\Schema\Schema;
use PHPUnit\Framework\TestCase;

class MapOrganizersTest extends TestCase
{
    /**
     * @testdox returns an array of Organization objects
     */
    public function testMapReturnsArrayOfOrganizerObjects(): void
    {
        $event = Schema::event()->organizer([Schema::organization(), Schema::person()]);

        $mapper = new MapOrganizers();
        $result = $mapper->map($event);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertInstanceOf(Organization::class, $result[0]);
    }

    /**
     * @testdox converts single organizer to array
     */
    public function testMapConvertsSingleOrganizerToArray(): void
    {
        $event = Schema::event()->organizer(Schema::organization());

        $mapper = new MapOrganizers();
        $result = $mapper->map($event);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertInstanceOf(Organization::class, $result[0]);
    }
}
