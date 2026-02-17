<?php

namespace Municipio\Controller;

use ComponentLibrary\Integrations\Image\Image as ImageComponentContract;
use DateTime;
use Modularity\Integrations\Component\ImageResolver;
use Municipio\Controller\SingularEvent\Contracts\PriceListItemInterface;
use Municipio\Schema\BaseType;
use Municipio\Schema\ImageObject;
use Municipio\Schema\Place;
use Municipio\Schema\Schema;
use Municipio\SchemaData\Utils\OpeningHoursSpecificationToString\OpeningHoursSpecificationToString;

/**
 * Controller for posts with ExhibitionEvent schema type.
 */
class SingularExhibitionEvent extends Singular
{
    private const GALLERY_THUMBNAIL_SIZE = [450, 280];
    private const GALLERY_LARGE_SIZE = [768, 432];

    protected object $postMeta;
    public string $view = 'single-schema-exhibition-event';

    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();

        $this->populateLanguageObject();

        $event = $this->post->getSchema();

        $this->data = array_merge($this->data, [
            'displayFeaturedImage' => false,
            'placeUrl' => $this->getPlaceUrl($event->getProperty('location')),
            'placeName' => $this->getPlaceName($event->getProperty('location')),
            'placeAddress' => $this->getPlaceAddress($event->getProperty('location')),
            'description' => $this->getDescription($event),
            'priceListItems' => $this->getPriceList(),
            'occasion' => $this->getOccasionText($event->getProperty('startDate'), $event->getProperty('endDate')),
            'physicalAccessibilityFeatures' => $this->formatAccessibilityFeatures($this->post->getSchemaProperty('physicalAccessibilityFeatures')),
            'eventIsInThePast' => $this->eventIsInThePast(),
            'galleryComponentAttributes' => $this->getGalleryComponentAttributes(),
            'openingHours' => $this->formatOpeningHours($event->getProperty('location')?->getProperty('openingHoursSpecification') ?? []),
            'specialOpeningHours' => $this->formatOpeningHours($event->getProperty('location')?->getProperty('specialOpeningHoursSpecification') ?? []),
        ]);
    }

    private function getPlaceName(?Place $place): ?string
    {
        if (!$place) {
            return null;
        }
        return $place['name'] ?? $place['address'] ?? null;
    }

    private function getPlaceAddress(?Place $place): ?string
    {
        return $place?->getProperty('address') ?? null;
    }

    private function getDescription(BaseType $event): ?string
    {
        $description = $event->getProperty('description');

        if (!is_string($description) || empty($description)) {
            return null;
        }

        return $this->wpService->wpautop($description);
    }

    private function formatAccessibilityFeatures(?array $features): ?string
    {
        return empty($features) ? null : implode(', ', $features);
    }

    private function formatOpeningHours(array $openingHours): ?string
    {
        if (empty($openingHours)) {
            return null;
        }

        $converter = new OpeningHoursSpecificationToString($this->wpService);
        $specs = array_map([$this, 'createOpeningHoursSpecification'], $openingHours);

        $formatted = array_map([$converter, 'convert'], $specs);
        return implode(', <br>', array_merge(...$formatted));
    }

    private function createOpeningHoursSpecification(array $item)
    {
        return Schema::openingHoursSpecification()
            ->setProperty('name', $item['name'] ?? null)
            ->setProperty('dayOfWeek', $item['dayOfWeek'] ?? null)
            ->setProperty('opens', $item['opens'] ?? null)
            ->setProperty('closes', $item['closes'] ?? null);
    }

    private function populateLanguageObject(): void
    {
        foreach ([
            'placeTitle' => $this->wpService->_x('Place', 'ExhibitionEvent', 'municipio'),
            'expiredEventNotice' => $this->wpService->_x('This event has already taken place.', 'ExhibitionEvent', 'municipio'),
            'dateLabel' => $this->wpService->_x('Date', 'ExhibitionEvent', 'municipio'),
            'openingHoursLabel' => $this->wpService->_x('Opening hours', 'ExhibitionEvent', 'municipio'),
            'specialOpeningHoursLabel' => $this->wpService->_x('Special opening hours', 'ExhibitionEvent', 'municipio'),
            'entranceLabel' => $this->wpService->_x('Entrance', 'ExhibitionEvent', 'municipio'),
            'accessibilityLabel' => $this->wpService->_x('Accessibility', 'ExhibitionEvent', 'municipio'),
            'directionsLabel' => $this->wpService->_x('Directions', 'ExhibitionEvent', 'municipio'),
            'galleryLabel' => $this->wpService->_x('Gallery', 'ExhibitionEvent', 'municipio'),
            'expiredDateNotice' => $this->wpService->_x('This exhibition has already taken place.', 'ExhibitionEvent', 'municipio'),
        ] as $key => $text) {
            $this->data['lang']->{$key} = $text;
        }
    }

    public function getPlaceUrl(?Place $place = null): ?string
    {
        if (!$place || !isset($place['geo']['address'])) {
            return null;
        }
        $googleMapsUrl = 'https://www.google.com/maps/search/?api=1&query=';
        return $googleMapsUrl . urlencode($place['geo']['address']);
    }

    public function getOccasionText(?DateTime $startDate, ?DateTime $endDate): string
    {
        if (!$startDate) {
            return '';
        }

        $start = ucfirst($this->wpService->dateI18n('j M', $startDate->getTimestamp()));
        $end = !$endDate ? $this->wpService->_x('until further notice', 'ExhibitionEvent', 'municipio') : ucfirst($this->wpService->dateI18n('j M Y', $endDate->getTimestamp()));

        return "{$start} - {$end}";
    }

    public function getPriceList(): array
    {
        $offers = $this->post->getSchemaProperty('offers');
        if (!$offers) {
            return [];
        }
        return array_map([$this, 'createPriceListItem'], $offers);
    }

    private function createPriceListItem(array $offer): PriceListItemInterface
    {
        $priceSpecification = $offer['priceSpecification'] ?? [];
        $name = $offer['name'] ?? '';
        $currency = $offer['priceCurrency'] ?? '';

        $price = $this->resolvePrice($offer, $priceSpecification, $currency);

        if (isset($offer['price']) && $offer['price'] === 0) {
            $price = $this->wpService->_x('Free entrance', 'ExhibitionEvent', 'municipio');
        }

        return new \Municipio\Controller\SingularEvent\PriceListItem($name, $price);
    }

    private function resolvePrice(array $offer, array $priceSpecification, string $currency): string
    {
        if (isset($priceSpecification['minPrice'], $priceSpecification['maxPrice'])) {
            if ($priceSpecification['minPrice'] === $priceSpecification['maxPrice']) {
                return "{$priceSpecification['minPrice']} {$currency}";
            }
            return "{$priceSpecification['minPrice']} - {$priceSpecification['maxPrice']} {$currency}";
        }
        if (isset($priceSpecification['price'])) {
            return "{$priceSpecification['price']} {$currency}";
        }
        if (isset($offer['price'])) {
            return "{$offer['price']} {$currency}";
        }
        return $this->wpService->__('Price not available', 'municipio');
    }

    public function eventIsInThePast(): bool
    {
        $event = $this->post->getSchema();
        $endDate = $event->getProperty('endDate');
        return $endDate instanceof DateTime && $endDate->getTimestamp() < time();
    }

    private function getGalleryComponentAttributes(): ?array
    {
        $imageProperty = $this->post->getSchemaProperty('image');

        if (is_array($imageProperty) && !empty($imageProperty)) {
            array_shift($imageProperty);
        }

        $imageAttributes = array_map([$this, 'getImageAttributes'], $imageProperty);

        return (
            empty($imageAttributes)
                ? null
                : [
                    'list' => $imageAttributes,
                ]
        );
    }

    private function getImageAttributes(ImageObject $image): array
    {
        $imageId = $image->getProperty('@id');
        if (empty($imageId)) {
            return [];
        }

        $smallContract = ImageComponentContract::factory($imageId, self::GALLERY_THUMBNAIL_SIZE, new ImageResolver());
        $largeContract = ImageComponentContract::factory($imageId, self::GALLERY_LARGE_SIZE, new ImageResolver());

        return [
            'largeImage' => $largeContract->getUrl(),
            'smallImage' => $smallContract->getUrl(),
            'alt' => $image->getProperty('description') ?: '',
            'caption' => null,
        ];
    }
}
