<?php

namespace Municipio\Controller\ArchiveEvent;

use Municipio\Schema\Event;
use Municipio\Schema\Offer;
use Municipio\Schema\PriceSpecification;

class GetEventPriceRange
{
    public static function getEventPriceRange(Event $event): ?string
    {
        $offers              = EnsureArrayOf::ensureArrayOf($event->getProperty('offers'), Offer::class);
        $priceSpecifications = array_map(fn($offer) => $offer->getProperty('priceSpecification'), $offers);
        $priceSpecifications = array_merge(...array_map(fn($spec) => EnsureArrayOf::ensureArrayOf($spec, PriceSpecification::class), $priceSpecifications));
        $prices              = array_map((fn(PriceSpecification $spec) => $spec->getProperty('price')), $priceSpecifications);
        $prices              = array_filter(array_map(fn($price) => is_numeric($price) ? floatval($price) : null, $prices));

        $minPrice = !empty($prices) ? min($prices) : null;
        $maxPrice = !empty($prices) ? max($prices) : null;
        $currency = !empty($priceSpecifications) ? $priceSpecifications[0]->getProperty('priceCurrency') : '';

        if ($currency === 'SEK') {
            $currency = 'kr';
        }

        // return range if we found any prices from price specifications
        if (is_numeric($minPrice) && is_numeric($maxPrice)) {
            if ($minPrice === $maxPrice) {
                return number_format($minPrice, 0, ',', ' ') . ' ' . $currency;
            }

            return number_format($minPrice, 0, ',', ' ') . '-' . number_format($maxPrice, 0, ',', ' ') . ' ' . $currency;
        }

        return null;
    }
}
