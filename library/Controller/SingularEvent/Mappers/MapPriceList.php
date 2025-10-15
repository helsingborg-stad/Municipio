<?php

namespace Municipio\Controller\SingularEvent\Mappers;

use Generator;
use Municipio\Controller\SingularEvent\Contracts\PriceListItemInterface;
use Municipio\Controller\SingularEvent\PriceListItem;
use Municipio\Helper\EnsureArrayOf\EnsureArrayOf;
use Municipio\Schema\Event;
use Municipio\Schema\Offer;
use Municipio\Schema\PriceSpecification;
use WpService\Contracts\__;

/**
 * Maps event data to a price list format.
 */
class MapPriceList implements EventDataMapperInterface
{
    /**
     * @param __ $wpService
     */
    public function __construct(private __ $wpService)
    {
    }

    /**
     * Maps the event data to a price list format.
     */
    public function map(Event $event): array
    {
        $offers         = EnsureArrayOf::ensureArrayOf($event->getProperty('offers'), Offer::class);
        $priceListItems = array_map(fn(Offer $offer) => iterator_to_array($this->getPriceListItemFromOffer($offer)), $offers);

        return array_filter(array_merge(...$priceListItems));
    }

    /**
     * @param Offer $offer
     * @return Generator<PriceListItemInterface>
     */
    public function getPriceListItemFromOffer(Offer $offer): Generator
    {
        $specs = EnsureArrayOf::ensureArrayOf($offer->getProperty('priceSpecification'), PriceSpecification::class);

        foreach ($specs as $spec) {
            $name     = $spec->getProperty('name');
            $currency = $this->getCurrencySymbol($spec->getProperty('priceCurrency') ?? $offer->getProperty('priceCurrency') ?? 'SEK');
            $minPrice = $spec->getProperty('minPrice');
            $maxPrice = $spec->getProperty('maxPrice');
            $price    = $spec->getProperty('price');

            $formattedPrice = $this->formatPrice($minPrice, $maxPrice, $price, $currency);

            if (!empty($name) && !empty($formattedPrice)) {
                yield new PriceListItem($name, $formattedPrice);
            }
        }

        // Removed bare yield to avoid yielding unnecessary null.
    }

    /**
     * Formats the price information for display.
     */
    private function formatPrice($minPrice, $maxPrice, $price, $currency): ?string
    {
        if ($minPrice !== null || $maxPrice !== null) {
            if ($minPrice === $maxPrice) {
                return "{$minPrice} {$currency}";
            }
            return "{$minPrice} - {$maxPrice} {$currency}";
        }

        if (!is_null($price)) {
            return "{$price} {$currency}";
        }

        return null;
    }

    /**
     * Gets the currency symbol for a given currency code.
     */
    private function getCurrencySymbol(string $currency): string
    {
        return match ($currency) {
            'SEK' => 'kr',
            default => $currency,
        };
    }
}
