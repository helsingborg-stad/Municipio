<?php

namespace Municipio\PostsList\ViewUtilities\Schema\Event;

use Municipio\Helper\EnsureArrayOf\EnsureArrayOf;
use Municipio\PostObject\PostObjectInterface;
use Municipio\PostsList\ViewUtilities\ViewUtilityInterface;
use Municipio\Schema\Event;
use Municipio\Schema\Offer;
use Municipio\Schema\PriceSpecification;

/**
 * Class GetPriceRange
 *
 * Provides functionality to calculate and return the price range for an event.
 */
class GetPriceRange implements ViewUtilityInterface
{
    public function getCallable(): callable
    {
        return fn(PostObjectInterface $post): ?string => $this->getPriceRangeFromPost($post);
    }

    private function getPriceRangeFromPost(PostObjectInterface $post): ?string
    {
        $offers = EnsureArrayOf::ensureArrayOf($post->getSchemaProperty('offers'), Offer::class);

        if (empty($offers)) {
            return null;
        }

        return $this->getPriceRangeFromOffers(...$offers);
    }

    /**
     * Calculates the price range for the given event based on its offers and price specifications.
     *
     * @param Offer ...$offers The offers associated with the event.
     * @return string|null The formatted price range string, or null if no prices are found.
     */
    private function getPriceRangeFromOffers(Offer ...$offers): ?string
    {
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
