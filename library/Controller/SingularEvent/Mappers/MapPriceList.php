<?php

namespace Municipio\Controller\SingularEvent\Mappers;

use Municipio\Controller\SingularEvent\Contracts\PriceListItemInterface;
use Municipio\Controller\SingularEvent\PriceListItem;
use Municipio\Schema\Event;
use Municipio\Schema\Offer;
use WpService\Contracts\__;

class MapPriceList implements EventDataMapperInterface
{
    public function __construct(private __ $wpService)
    {
    }

    public function map(Event $event): array
    {
        $offers = $event->getProperty('offers');

        if (!$offers || !is_array($offers)) {
            return [];
        }

        return array_filter(array_map([$this, 'getPriceListItemFromOffer'], $offers));
    }

    public function getPriceListItemFromOffer(Offer $offer): ?PriceListItemInterface
    {
        $priceSpecification = $offer->getProperty('priceSpecification');
        $name               = $offer->getProperty('name');
        $currency           = $offer->getProperty('priceCurrency') ?? 'SEK';

        $minPrice           = $priceSpecification?->getProperty('minPrice');
        $maxPrice           = $priceSpecification?->getProperty('maxPrice');
        $offerPrice         = $offer->getProperty('price');
        $specificationPrice = $priceSpecification?->getProperty('price');

        $price = $this->formatPrice($minPrice, $maxPrice, $specificationPrice, $offerPrice, $currency);

        if (empty($name) || empty($price)) {
            return null;
        }

        return new PriceListItem($name, $price);
    }

    private function formatPrice($minPrice, $maxPrice, $specificationPrice, $offerPrice, $currency): string
    {
        if (!empty($minPrice) || !empty($maxPrice)) {
            if ($minPrice === $maxPrice) {
                return "{$minPrice} {$currency}";
            }
            return "{$minPrice} - {$maxPrice} {$currency}";
        }

        if (!empty($specificationPrice)) {
            return "{$specificationPrice} {$currency}";
        }

        if (!empty($offerPrice)) {
            return "{$offerPrice} {$currency}";
        }

        return $this->wpService->__('Price not available', 'municipio');
    }
}
