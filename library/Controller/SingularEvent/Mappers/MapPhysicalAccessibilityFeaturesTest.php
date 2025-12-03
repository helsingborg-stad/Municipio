<?php

namespace Municipio\Controller\SingularEvent\Mappers;

use Municipio\Schema\Schema;
use PHPUnit\Framework\TestCase;

class MapPhysicalAccessibilityFeaturesTest extends TestCase
{
    #[TestDox('returns an array of physical accessibility features')]
    public function testMapReturnsArrayOfPhysicalAccessibilityFeatures(): void
    {
        $event  = Schema::event()->physicalAccessibilityFeatures(['Wheelchair accessible', 'Braille signage']);
        $mapper = new MapPhysicalAccessibilityFeatures();
        $result = $mapper->map($event);

        $this->assertEquals(['Wheelchair accessible', 'Braille signage'], $result);
    }

    #[TestDox('converts string to array')]
    public function testMapConvertsStringToArray(): void
    {
        $event  = Schema::event()->physicalAccessibilityFeatures('Wheelchair accessible');
        $mapper = new MapPhysicalAccessibilityFeatures();
        $result = $mapper->map($event);

        $this->assertEquals(['Wheelchair accessible'], $result);
    }
}
