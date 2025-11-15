<?php

namespace Municipio\Controller\SingularEvent;

use Municipio\Helper\EnsureArrayOf\EnsureArrayOf;
use Municipio\Schema\Event;
use Municipio\Schema\Offer;
use Municipio\Schema\PriceSpecification;

/**
 * Class GetEventPriceRange
 *
 * Provides functionality to calculate and return the price range for an event.
 */
class GetEventPriceRange
{
    /**
     * Calculates the price range for the given event based on its offers and price specifications.
     *
     * @param Event $event The event object containing offers and price specifications.
     * @return string|null The formatted price range string, or null if no prices are found.
     */
    public static function getEventPriceRange(Event $event): ?string
    {
        $offers = EnsureArrayOf::ensureArrayOf($event->getProperty('offers'), Offer::class);

        $priceSpecifications = [];
        foreach ($offers as $offer) {
            $specs               = EnsureArrayOf::ensureArrayOf($offer->getProperty('priceSpecification'), PriceSpecification::class);
            $priceSpecifications = array_merge($priceSpecifications, $specs);
        }

        $prices = [];
        foreach ($priceSpecifications as $spec) {
            $price = $spec->getProperty('price');
            if (is_numeric($price)) {
                $prices[] = floatval($price);
            }
        }

        $minPrice = !empty($prices) ? min($prices) : null;
        $maxPrice = !empty($prices) ? max($prices) : null;

        $currency = '';
        if (!empty($priceSpecifications)) {
            $currency = $priceSpecifications[0]->getProperty('priceCurrency');
            if ($currency === 'SEK') {
                $currency = 'kr';
            }
        }

        if (is_numeric($minPrice) && is_numeric($maxPrice)) {
            $formattedMin = number_format($minPrice, 0, ',', ' ');
            $formattedMax = number_format($maxPrice, 0, ',', ' ');

            if ($minPrice === $maxPrice) {
                return "{$formattedMin} {$currency}";
            }

            return "{$formattedMin}-{$formattedMax} {$currency}";
        }

        return null;
    }
}
