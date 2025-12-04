<?php

namespace Municipio\PostsList\ViewCallableProviders\Schema\Event;

use Municipio\Schema\Schema;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class GetPriceRangeTest extends TestCase
{
    #[TestDox("Returns single price with currency")]
    public function testReturnsSinglePriceWithCurrency(): void
    {
        $post = new class extends \Municipio\PostObject\NullPostObject {
            public function getSchemaProperty(string $property): mixed
            {
                if ($property === 'offers') {
                    return [
                        Schema::offer()->priceSpecification([
                            Schema::priceSpecification()->price(100)->priceCurrency('EUR')
                        ])
                    ];
                }

                return null;
            }
        };

        $getPriceRange = new GetPriceRange();

        $this->assertEquals('100 EUR', $getPriceRange->getCallable()($post));
    }

    #[TestDox("Returns price range with currency")]
    public function testReturnsPriceRangeWithCurrency(): void
    {
        $post = new class extends \Municipio\PostObject\NullPostObject {
            public function getSchemaProperty(string $property): mixed
            {
                if ($property === 'offers') {
                    return [
                        Schema::offer()->priceSpecification([
                            Schema::priceSpecification()->price(100)->priceCurrency('EUR'),
                            Schema::priceSpecification()->price(200)->priceCurrency('EUR')
                        ])
                    ];
                }

                return null;
            }
        };

        $getPriceRange = new GetPriceRange();

        $this->assertEquals('100-200 EUR', $getPriceRange->getCallable()($post));
    }

    #[TestDox("Converts SEK currency to kr")]
    public function testConvertsSEKCurrencyToKr(): void
    {
        $post = new class extends \Municipio\PostObject\NullPostObject {
            public function getSchemaProperty(string $property): mixed
            {
                if ($property === 'offers') {
                    return [
                        Schema::offer()->priceSpecification([
                            Schema::priceSpecification()->price(150)->priceCurrency('SEK')
                        ])
                    ];
                }

                return null;
            }
        };

        $getPriceRange = new GetPriceRange();
        $result        = $getPriceRange->getCallable()($post);

        $this->assertEquals('150 kr', $result);
    }

    #[TestDox("Returns null if no offers")]
    public function testReturnsNullIfNoOffers(): void
    {
        $post = new class extends \Municipio\PostObject\NullPostObject {
            public function getSchemaProperty(string $property): mixed
            {
                return null;
            }
        };

        $getPriceRange = new GetPriceRange();
        $result        = $getPriceRange->getCallable()($post);

        $this->assertNull($result);
    }

    #[TestDox("Returns null if no numeric prices")]
    public function testReturnsNullIfNoNumericPrices(): void
    {
        $post = new class extends \Municipio\PostObject\NullPostObject {
            public function getSchemaProperty(string $property): mixed
            {
                if ($property === 'offers') {
                    return [
                        Schema::offer()->priceSpecification([
                            Schema::priceSpecification()->price('not-a-number')->priceCurrency('EUR')
                        ])
                    ];
                }

                return null;
            }
        };

        $getPriceRange = new GetPriceRange();
        $result        = $getPriceRange->getCallable()($post);
        $this->assertNull($result);
    }

    #[TestDox("Handles multiple offers with multiple price specifications")]
    public function testHandlesMultipleOffersWithMultiplePriceSpecifications(): void
    {
        $post          = new class extends \Municipio\PostObject\NullPostObject {
            public function getSchemaProperty(string $property): mixed
            {
                if ($property === 'offers') {
                    return [
                        Schema::offer()->priceSpecification([
                            Schema::priceSpecification()->price(50)->priceCurrency('EUR'),
                            Schema::priceSpecification()->price(100)->priceCurrency('EUR')
                        ]),
                        Schema::offer()->priceSpecification([
                            Schema::priceSpecification()->price(200)->priceCurrency('EUR')
                        ])
                    ];
                }

                return null;
            }
        };
        $getPriceRange = new GetPriceRange();
        $result        = $getPriceRange->getCallable()($post);

        $this->assertEquals('50-200 EUR', $result);
    }
}
