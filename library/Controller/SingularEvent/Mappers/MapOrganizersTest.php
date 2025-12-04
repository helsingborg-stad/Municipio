<?php

namespace Municipio\Controller\SingularEvent\Mappers;

use Municipio\Schema\Organization;
use Municipio\Schema\Schema;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class MapOrganizersTest extends TestCase
{
    #[TestDox('returns an array of Organization objects')]
    public function testMapReturnsArrayOfOrganizerObjects(): void
    {
        $event = Schema::event()->organizer([Schema::organization()->name('Org 1'), Schema::person()]);

        $mapper = new MapOrganizers();
        $result = $mapper->map($event);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertInstanceOf(Organization::class, $result[0]);
    }

    #[TestDox('converts single organizer to array')]
    public function testMapConvertsSingleOrganizerToArray(): void
    {
        $event = Schema::event()->organizer(Schema::organization()->name('Single Organizer'));

        $mapper = new MapOrganizers();
        $result = $mapper->map($event);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertInstanceOf(Organization::class, $result[0]);
    }

    #[TestDox('returns empty array when no valid organizers are present')]
    public function testMapReturnsEmptyArrayWhenNoOrganizersPresent(): void
    {
        $organizerWithoutName = Schema::organization();
        $event                = Schema::event()->organizer([$organizerWithoutName]);

        $mapper = new MapOrganizers();
        $result = $mapper->map($event);

        $this->assertIsArray($result);
        $this->assertCount(0, $result);
    }
}
