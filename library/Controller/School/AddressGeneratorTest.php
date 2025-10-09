<?php

declare(strict_types=1);

namespace Municipio\Controller\School;

use Municipio\Schema\Place;
use Municipio\Schema\Schema;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\_x;
use WpService\Implementations\FakeWpService;

class AddressGeneratorTest extends TestCase
{
    public function testCanBeInstantiated(): void
    {
        $preschool = \Municipio\Schema\Schema::preschool();
        $generator = new AddressGenerator($preschool, $this->getWpService());
        $this->assertInstanceOf(AddressGenerator::class, $generator);
    }

    public function testGenerateReturnsNullAddressAndDirectionsIfAddressIsEmpty(): void
    {
        $preschool = \Municipio\Schema\Schema::preschool()->location([
            Schema::place(), // No address
            Schema::place()->address(''), // Empty address
        ]);
        $generator = new AddressGenerator($preschool, $this->getWpService());
        $result    = $generator->generate();
        $this->assertNull($result);
        $this->assertNull($result);
    }

    public function testGenerateReturnsValidAddressAndDirections(): void
    {
        $address   = 'Testgatan 1, 12345 Teststad';
        $preschool = \Municipio\Schema\Schema::preschool()->location([
            Schema::place()->address($address)
        ]);
        $generator = new AddressGenerator($preschool, $this->getWpService());
        $result    = $generator->generate();
        $this->assertSame($address, $result[0]['address']);
        $this->assertEquals([
            'label' => 'Get directions',
            'href'  => 'https://www.google.com/maps/dir//' . urlencode($address)
        ], $result[0]['directionsLink']);
    }

    private function getWpService(): _x
    {
        return new FakeWpService(['_x' => function ($text, $context, $domain) {
            return $text;
        }]);
    }
}
