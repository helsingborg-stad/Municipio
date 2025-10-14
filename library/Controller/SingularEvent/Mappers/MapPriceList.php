<?php

namespace Municipio\Controller\SingularEvent\Mappers;

use Generator;
use Municipio\Controller\SingularEvent\Contracts\PriceListItemInterface;
use Municipio\Controller\SingularEvent\PriceListItem;
use Municipio\Schema\Event;
use Municipio\Schema\Offer;
use Municipio\Schema\PriceSpecification;
use WpService\Contracts\__;

class MapPriceList implements EventDataMapperInterface
{
    public function __construct(private __ $wpService)
    {
    }

    public function map(Event $event): array
    {
        $offers         = $this->ensureArrayOf($event->getProperty('offers'), Offer::class);
        $priceListItems = array_map(fn(Offer $offer) => iterator_to_array($this->getPriceListItemFromOffer($offer)), $offers);

        return array_filter($priceListItems[0]);
    }

    /**
     * @param Offer $offer
     * @return Generator<PriceListItemInterface>
     */
    public function getPriceListItemFromOffer(Offer $offer): Generator
    {
        $specs = $this->ensureArrayOf($offer->getProperty('priceSpecification'), PriceSpecification::class);

        foreach ($specs as $spec) {
            $name     = $spec->getProperty('name');
            $currency = $this->getCurrencySymbol($offer->getProperty('priceCurrency') ?? 'SEK');
            $minPrice = $spec->getProperty('minPrice');
            $maxPrice = $spec->getProperty('maxPrice');
            $price    = $spec->getProperty('price');

            $formattedPrice = $this->formatPrice($minPrice, $maxPrice, $price, $currency);

            if (!empty($name) && !empty($formattedPrice)) {
                yield new PriceListItem($name, $formattedPrice);
            }
        }

        yield;
    }

    private function formatPrice($minPrice, $maxPrice, $price, $currency): ?string
    {
        if (!empty($minPrice) || !empty($maxPrice)) {
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

    private function ensureArrayOf($value, $ensuredType): array
    {
        if (!is_array($value)) {
            $value = [$value];
        }

        return array_filter($value, fn($item) => is_a($item, $ensuredType));
    }

    private function getCurrencySymbol(string $currency): string
    {
        return match ($currency) {
            'SEK' => 'kr',
            default => $currency,
        };
    }
}
