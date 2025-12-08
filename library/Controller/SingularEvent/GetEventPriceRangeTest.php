<?php

namespace Municipio\Controller\SingularEvent;

use Municipio\Schema\Schema;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class GetEventPriceRangeTest extends TestCase
{
    #[TestDox("Returns single price with currency")]
    public function testReturnsSinglePriceWithCurrency(): void
    {
        $event = Schema::event()->offers([
            Schema::offer()->priceSpecification([
                Schema::priceSpecification()->price(100)->priceCurrency('EUR')
            ])
        ]);

        $result = GetEventPriceRange::getEventPriceRange($event);

        $this->assertEquals('100 EUR', $result);
    }

    #[TestDox("Returns price range with currency")]
    public function testReturnsPriceRangeWithCurrency(): void
    {
        $event = Schema::event()->offers([
            Schema::offer()->priceSpecification([
                Schema::priceSpecification()->price(100)->priceCurrency('EUR'),
                Schema::priceSpecification()->price(200)->priceCurrency('EUR')
            ])
        ]);

        $result = GetEventPriceRange::getEventPriceRange($event);

        $this->assertEquals('100-200 EUR', $result);
    }

    #[TestDox("Converts SEK currency to kr")]
    public function testConvertsSEKCurrencyToKr(): void
    {
        $event = Schema::event()->offers([
            Schema::offer()->priceSpecification([
                Schema::priceSpecification()->price(150)->priceCurrency('SEK')
            ])
        ]);

        $result = GetEventPriceRange::getEventPriceRange($event);

        $this->assertEquals('150 kr', $result);
    }

    #[TestDox("Returns null if no offers")]
    public function testReturnsNullIfNoOffers(): void
    {
        $event = Schema::event()->offers([]);

        $result = GetEventPriceRange::getEventPriceRange($event);

        $this->assertNull($result);
    }

    #[TestDox("Returns null if no numeric prices")]
    public function testReturnsNullIfNoNumericPrices(): void
    {
        $event = Schema::event()->offers([
            Schema::offer()->priceSpecification([
                Schema::priceSpecification()->price('not-a-number')->priceCurrency('EUR')
            ])
        ]);

        $result = GetEventPriceRange::getEventPriceRange($event);

        $this->assertNull($result);
    }

    #[TestDox("Handles multiple offers with multiple price specifications")]
    public function testHandlesMultipleOffersWithMultiplePriceSpecifications(): void
    {
        $event = Schema::event()->offers([
            Schema::offer()->priceSpecification([
                Schema::priceSpecification()->price(50)->priceCurrency('EUR'),
                Schema::priceSpecification()->price(100)->priceCurrency('EUR')
            ]),
            Schema::offer()->priceSpecification([
                Schema::priceSpecification()->price(200)->priceCurrency('EUR')
            ])
        ]);

        $result = GetEventPriceRange::getEventPriceRange($event);

        $this->assertEquals('50-200 EUR', $result);
    }
}
