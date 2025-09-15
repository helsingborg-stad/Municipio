<?php

declare(strict_types=1);

namespace Municipio\Controller\SingularElementarySchool;

use PHPUnit\Framework\TestCase;

class MapComponentAttributesGeneratorTest extends TestCase
{
    public function testCanBeInstantiated(): void
    {
        $elementarySchool = \Municipio\Schema\Schema::elementarySchool();
        $generator        = new MapComponentAttributesGenerator($elementarySchool);
        $this->assertInstanceOf(MapComponentAttributesGenerator::class, $generator);
    }

    public function testGenerateReturnsNullIfLatitudeOrLongitudeMissing(): void
    {
        $elementarySchool = \Municipio\Schema\Schema::elementarySchool()->latitude(null)->longitude(59.33);
        $generator        = new MapComponentAttributesGenerator($elementarySchool);
        $this->assertNull($generator->generate());

        $elementarySchool = \Municipio\Schema\Schema::elementarySchool()->latitude(18.06)->longitude(null);
        $generator        = new MapComponentAttributesGenerator($elementarySchool);
        $this->assertNull($generator->generate());
    }

    public function testGenerateReturnsValidPinsAndStartPosition(): void
    {
        $elementarySchool = \Municipio\Schema\Schema::elementarySchool()->latitude(59.33)->longitude(18.06);
        $generator        = new MapComponentAttributesGenerator($elementarySchool);
        $result           = $generator->generate();
        $this->assertEquals([
            'pins'          => [ [ 'lat' => 59.33, 'lng' => 18.06 ] ],
            'startPosition' => [ 'lat' => 59.33, 'lng' => 18.06, 'zoom' => 14 ],
        ], $result);
    }
}
