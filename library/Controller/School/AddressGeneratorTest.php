<?php

declare(strict_types=1);

namespace Municipio\Controller\School;


use Municipio\Schema\Schema;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\_x;
use WpService\Implementations\FakeWpService;

class AddressGeneratorTest extends TestCase
{
    public function testCanBeInstantiated(): void
    {
        $preschool = \Municipio\Schema\Schema::preschool();
        $generator = new AddressGenerator($preschool, $this->getWpService());
        static::assertInstanceOf(AddressGenerator::class, $generator);
    }

    public function testGenerateReturnsNullAddressAndDirectionsIfAddressIsEmpty(): void
    {
        $preschool = \Municipio\Schema\Schema::preschool()->location([
            Schema::place(), // No address
            Schema::place()->address(''), // Empty address
        ]);
        $generator = new AddressGenerator($preschool, $this->getWpService());
        $result    = $generator->generate();
        static::assertNull($result);
        static::assertNull($result);
    }

    public function testGenerateReturnsValidAddressAndDirections(): void
    {
        $address   = 'Testgatan 1, 12345 Teststad';
        $preschool = \Municipio\Schema\Schema::preschool()->location([
            Schema::place()->address($address)
        ]);
        $generator = new AddressGenerator($preschool, $this->getWpService());
        $result    = $generator->generate();
        static::assertSame($address, $result[0]['address']);
        static::assertEquals([
            'label' => 'Get directions',
            'href'  => 'https://www.google.com/maps/dir//' . urlencode($address)
        ], $result[0]['directionsLink']);
    }

    #[TestDox('description is null if not available')]
    public function testDescriptionIsNullIfNotAvailable(): void
    {
        $address   = 'Testgatan 1, 12345 Teststad';
        $place = Schema::place()->address($address);
        $preschool = \Municipio\Schema\Schema::preschool()->location([ $place ]);
        
        $generator = new AddressGenerator($preschool, $this->getWpService());
        $result    = $generator->generate();

        static::assertNull($result[0]['description']);
    }

    #[TestDox('description is as string if available')]
    public function testDescriptionIsAsStringIfAvailable(): void
    {
        $address   = 'Testgatan 1, 12345 Teststad';
        $description = 'This is a test description for the place.';
        $place = Schema::place()->address($address)->description($description);
        $preschool = \Municipio\Schema\Schema::preschool()->location([ $place ]);
        
        $generator = new AddressGenerator($preschool, $this->getWpService());
        $result    = $generator->generate();

        static::assertSame($description, $result[0]['description']);
    }

    private function getWpService(): _x
    {
        return new FakeWpService(['_x' => static fn ($text, $context, $domain) => $text]);
    }
}
