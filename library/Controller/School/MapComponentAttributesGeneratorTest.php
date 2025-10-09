<?php

declare(strict_types=1);

namespace Municipio\Controller\School;

use Municipio\Schema\Schema;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class MapComponentAttributesGeneratorTest extends TestCase
{
    public function testCanBeInstantiated(): void
    {
        $preschool = Schema::preschool();
        $generator = new MapComponentAttributesGenerator($preschool);
        $this->assertInstanceOf(MapComponentAttributesGenerator::class, $generator);
    }

    #[TestDox('Generate method returns null if no valid coordinates are provided')]
    public function testGenerateReturnsNullIfLatitudeOrLongitudeMissing(): void
    {
        $preschool = Schema::preschool()->location([
            Schema::place(), // Missing both latitude and longitude
            Schema::place()->latitude(18.06), // Missing longitude
            Schema::place()->longitude(59.33), // Missing latitude
        ]);

        $generator = new MapComponentAttributesGenerator($preschool);

        $this->assertNull($generator->generate());
    }

    #[TestDox('Generate method returns valid pins when latitude and longitude are provided')]
    public function testGenerateReturnsValidPins(): void
    {
        $preschool = Schema::preschool()->location([
            Schema::place()->latitude(59.33)->longitude(18.06)->address('Testgatan 1, 12345 Teststad'),
            Schema::place()->latitude(60.00)->longitude(19.00)->address('Testgatan 2, 12345 Teststad'), // This one should be ignored
        ]);
        $generator = new MapComponentAttributesGenerator($preschool);
        $result    = $generator->generate();

        $expectedPins = [
            [
                'lat'     => 59.33,
                'lng'     => 18.06,
                'tooltip' => ['title' => 'Testgatan 1, 12345 Teststad'],
            ],
            [
                'lat'     => 60.00,
                'lng'     => 19.00,
                'tooltip' => ['title' => 'Testgatan 2, 12345 Teststad'],
            ],
        ];
        $this->assertEquals($expectedPins, $result['pins']);
    }

    #[TestDox('Generate method returns start position when valid coordinates are provided')]
    public function testGenerateReturnsStartPosition(): void
    {
        $preschool = Schema::preschool()->location([
            Schema::place()->latitude(59.33)->longitude(18.06),
            Schema::place()->latitude(60.00)->longitude(19.00),
        ]);
        $generator = new MapComponentAttributesGenerator($preschool);
        $result    = $generator->generate();

        $this->assertIsFloat($result['startPosition']['lat']);
        $this->assertIsFloat($result['startPosition']['lng']);
    }
}
