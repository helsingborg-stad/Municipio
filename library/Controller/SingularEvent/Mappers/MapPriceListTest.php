<?php

namespace Municipio\Controller\SingularEvent\Mappers;

use Municipio\Controller\SingularEvent\Contracts\PriceListItemInterface;
use Municipio\Schema\Schema;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\__;

class MapPriceListTest extends TestCase
{
    #[TestDox('returns an empty array when no price list is set')]
    public function testReturnsEmptyArrayWhenNoPriceListIsSet()
    {
        $mapper = new MapPriceList($this->getWpService());
        $event  = Schema::event();

        $result = $mapper->map($event);
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    #[TestDox('returns an array of PriceListItemInterface from offers')]
    public function testReturnsArrayOfPriceListItemInterfaceWhenPriceListIsSet()
    {
        $mapper = new MapPriceList($this->getWpService());
        $event  = Schema::event()->offers([
            Schema::offer()->priceSpecification([
                Schema::priceSpecification()->name('Standard Ticket')->price(100)->priceCurrency('SEK')
            ])
        ]);

        $result = $mapper->map($event);

        $this->assertIsArray($result);
        $this->assertInstanceOf(PriceListItemInterface::class, $result[0]);
    }

    private function getWpService(): __
    {
        return new class implements __ {
            public function __(string $text, string $domain = 'default'): string
            {
                return $text;
            }
        };
    }
}
