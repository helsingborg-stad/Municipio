<?php

declare(strict_types=1);

namespace Municipio\Controller\SingularPreschool;

use PHPUnit\Framework\TestCase;

class MapComponentAttributesGeneratorTest extends TestCase
{
    public function testCanBeInstantiated(): void
    {
        $preschool = \Municipio\Schema\Schema::preschool();
        $generator = new MapComponentAttributesGenerator($preschool);
        $this->assertInstanceOf(MapComponentAttributesGenerator::class, $generator);
    }

    public function testGenerateReturnsNullIfLatitudeOrLongitudeMissing(): void
    {
        $preschool = \Municipio\Schema\Schema::preschool()->latitude(null)->longitude(59.33);
        $generator = new MapComponentAttributesGenerator($preschool);
        $this->assertNull($generator->generate());

        $preschool = \Municipio\Schema\Schema::preschool()->latitude(18.06)->longitude(null);
        $generator = new MapComponentAttributesGenerator($preschool);
        $this->assertNull($generator->generate());
    }

    public function testGenerateReturnsValidPinsAndStartPosition(): void
    {
        $preschool = \Municipio\Schema\Schema::preschool()->latitude(59.33)->longitude(18.06);
        $generator = new MapComponentAttributesGenerator($preschool);
        $result    = $generator->generate();
        $this->assertEquals([
            'pins'          => [ [ 'lat' => 59.33, 'lng' => 18.06 ] ],
            'startPosition' => [ 'lat' => 59.33, 'lng' => 18.06, 'zoom' => 14 ],
        ], $result);
    }
}
