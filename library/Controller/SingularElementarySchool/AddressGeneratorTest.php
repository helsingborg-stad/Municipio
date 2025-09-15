<?php

declare(strict_types=1);

namespace Municipio\Controller\SingularElementarySchool;

use PHPUnit\Framework\TestCase;
use WpService\Contracts\_x;
use WpService\Implementations\FakeWpService;

class AddressGeneratorTest extends TestCase
{
    public function testCanBeInstantiated(): void
    {
        $elementarySchool = \Municipio\Schema\Schema::elementarySchool();
        $generator        = new AddressGenerator($elementarySchool, $this->getWpService());
        $this->assertInstanceOf(AddressGenerator::class, $generator);
    }

    public function testGenerateReturnsNullAddressAndDirectionsIfAddressIsEmpty(): void
    {
        $elementarySchool = \Municipio\Schema\Schema::elementarySchool()->address('');
        $generator        = new AddressGenerator($elementarySchool, $this->getWpService());
        $result           = $generator->generate();
        $this->assertNull($result['address']);
        $this->assertNull($result['directionsLink']);
    }

    public function testGenerateReturnsValidAddressAndDirections(): void
    {
        $address          = 'Testgatan 1, 12345 Teststad';
        $elementarySchool = \Municipio\Schema\Schema::elementarySchool()->address($address);
        $generator        = new AddressGenerator($elementarySchool, $this->getWpService());
        $result           = $generator->generate();
        $this->assertSame($address, $result['address']);
        $this->assertEquals([
            'label' => 'Get directions',
            'href'  => 'https://www.google.com/maps/dir//' . urlencode($address)
        ], $result['directionsLink']);
    }

    private function getWpService(): _x
    {
        return new FakeWpService(['_x' => function ($text, $context, $domain) {
            return $text;
        }]);
    }
}
