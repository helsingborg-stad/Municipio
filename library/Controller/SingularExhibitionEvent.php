<?php

namespace Municipio\Controller;

use DateTime;
use Municipio\Schema\BaseType;
use Municipio\Schema\ImageObject;
use Municipio\Schema\Place;
use Municipio\Schema\Schema;
use Modularity\Integrations\Component\ImageResolver;
use ComponentLibrary\Integrations\Image\Image as ImageComponentContract;
use Municipio\Controller\SingularEvent\Contracts\PriceListItemInterface;
use Municipio\SchemaData\Utils\OpeningHoursSpecificationToString\OpeningHoursSpecificationToString;

/**
 * Controller for posts with ExhibitionEvent schema type.
 */
class SingularExhibitionEvent extends Singular
{
    private const GALLERY_THUMBNAIL_SIZE = [450, 280];
    private const GALLERY_LARGE_SIZE     = [768, 432];
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

        $this->data['displayFeaturedImage']          = false;
        $this->data['placeUrl']                      = $this->getPlaceUrl($event->getProperty('location'));
        $this->data['placeName']                     = $this->getPlaceName($event->getProperty('location'));
        $this->data['placeAddress']                  = $this->getPlaceAddress($event->getProperty('location'));
        $this->data['priceListItems']                = $this->getPriceList();
        $this->data['occassion']                     = $this->getOccasionText($event->getProperty('startDate'), $event->getProperty('endDate'));
        $this->data['physicalAccessibilityFeatures'] = $this->getPhysicalAccessibilityFeaturesList($this->post->getSchemaProperty('physicalAccessibilityFeatures'));
        $this->data['eventIsInThePast']              = $this->eventIsInThePast();
        $this->data['galleryComponentAttributes']    = $this->getGalleryComponentAttributes();
        $this->data['openingHours']                  = $this->getOpeningHours($event->getProperty('location')?->getProperty('openingHoursSpecification') ?? []);
        $this->data['specialOpeningHours']           = $this->getOpeningHours($event->getProperty('location')?->getProperty('specialOpeningHoursSpecification') ?? []);

        $this->setHttpStatusHeaderIfPastEvent($event);
    }

    /**
     * Get the name of the place from the Place schema.
     */
    private function getPlaceName(?Place $place): ?string
    {
        if (!$place) {
            return null;
        }
        return $place['name'] ?? $place['address'] ?? null;
    }

    /**
     * Get the address of the place from the Place schema.
     */
    private function getPlaceAddress(?Place $place): ?string
    {
        return $place?->getProperty('geo')?->getProperty('address') ?? null;
    }

    /**
     * Get the physical accessibility features from the event schema.
     */
    private function getPhysicalAccessibilityFeaturesList(?array $features): ?string
    {
        return empty($features) ? null : implode(', ', $features);
    }

    /**
     * Get the opening hours from the event schema.
     */
    private function getOpeningHours(array $openingHours): ?array
    {
        if (empty($openingHours)) {
            return null;
        }

        $converter    = new OpeningHoursSpecificationToString($this->wpService);
        $openingHours = array_map(
            fn($item) => Schema::openingHoursSpecification()
                ->setProperty('name', $item['name'] ?? null)
                ->setProperty('dayOfWeek', $item['dayOfWeek'] ?? null)
                ->setProperty('opens', $item['opens'] ?? null)
                ->setProperty('closes', $item['closes'] ?? null),
            $openingHours
        );

        $formatted = array_map(fn($spec) => $converter->convert($spec), $openingHours);
        return array_merge(...$formatted);
    }

    /**
     * Populate the language object with translated strings.
     */
    private function populateLanguageObject(): void
    {
        $lang = $this->data['lang'];

        $lang->placeTitle               = $this->wpService->_x('Place', 'ExhibitionEvent', 'municipio');
        $lang->expiredEventNoticeLabel  = $this->wpService->_x('This event has already taken place.', 'ExhibitionEvent', 'municipio');
        $lang->dateLabel                = $this->wpService->_x('Date', 'ExhibitionEvent', 'municipio');
        $lang->openingHoursLabel        = $this->wpService->_x('Opening hours', 'ExhibitionEvent', 'municipio');
        $lang->specialOpeningHoursLabel = $this->wpService->_x('Special opening hours', 'ExhibitionEvent', 'municipio');
        $lang->entranceLabel            = $this->wpService->_x('Entrance', 'ExhibitionEvent', 'municipio');
        $lang->accessibilityLabel       = $this->wpService->_x('Accessibility', 'ExhibitionEvent', 'municipio');
        $lang->directionsLabel          = $this->wpService->_x('Directions', 'ExhibitionEvent', 'municipio');
        $lang->galleryLabel             = $this->wpService->_x('Gallery', 'ExhibitionEvent', 'municipio');
    }

    /**
     * Get the URL of the place from the Place schema.
     */
    public function getPlaceUrl(?Place $place = null): ?string
    {
        if (!$place || !isset($place['geo']['address'])) {
            return null;
        }
        $googleMapsUrl = 'https://www.google.com/maps/search/?api=1&query=';
        return $googleMapsUrl . urlencode($place['geo']['address']);
    }

    /**
     * Get the occasion text from the event schema.
     */
    public function getOccasionText(?DateTime $startDate, ?DateTime $endDate): string
    {
        if (!$startDate || !$endDate) {
            return '';
        }

        $start = ucfirst($this->wpService->dateI18n('j M', $startDate->getTimestamp()));
        $end   = ucfirst($this->wpService->dateI18n('j M Y', $endDate->getTimestamp()));

        return "{$start} - {$end}";
    }

    /**
     * Get the price list from the event schema.
     */
    public function getPriceList(): array
    {
        $offers = $this->post->getSchemaProperty('offers');
        if (!$offers) {
            return [];
        }
        return array_map([$this, 'getPriceListItemFromOffer'], $offers);
    }

    /**
     * Get a price list item from an offer.
     */
    public function getPriceListItemFromOffer(array $offer): PriceListItemInterface
    {
        $priceSpecification = $offer['priceSpecification'] ?? [];
        $name               = $offer['name'] ?? '';
        $currency           = $offer['priceCurrency'] ?? '';

        $price = $this->resolvePrice($offer, $priceSpecification, $currency);

        if ($offer['price'] === 0) {
            $price = $this->wpService->_x('Free entrance', 'ExhibitionEvent', 'municipio');
        }

        return new \Municipio\Controller\SingularEvent\PriceListItem($name, $price);
    }

    /**
     * Resolve the price from an offer.
     */
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

    /**
     * Check if the event is in the past.
     */
    public function eventIsInThePast(): bool
    {
        $event   = $this->post->getSchema();
        $endDate = $event->getProperty('endDate');
        return $endDate instanceof DateTime && $endDate->getTimestamp() < time();
    }

    /**
     * Set the HTTP status header if the event is in the past.
     */
    private function setHttpStatusHeaderIfPastEvent(BaseType $event): void
    {
        if ($this->eventIsInThePast()) {
            $this->wpService->statusHeader(410);
        }
    }

    /**
     * Get the gallery component attributes from the event schema.
     */
    private function getGalleryComponentAttributes(): ?array
    {
        $imageProperty = $this->post->getSchemaProperty('image');
        if (!is_array($imageProperty)) {
            return null;
        }
        return [
            'list' => array_map([$this, 'getImageAttributes'], $imageProperty)
        ];
    }

    /**
     * Get the image attributes from an ImageObject.
     */
    private function getImageAttributes(ImageObject $image): array
    {
        if (empty($image->getProperty('@id'))) {
            return [];
        }

        $smallContract = ImageComponentContract::factory($image->getProperty('@id'), self::GALLERY_THUMBNAIL_SIZE, new ImageResolver());
        $largeContract = ImageComponentContract::factory($image->getProperty('@id'), self::GALLERY_LARGE_SIZE, new ImageResolver());

        return [
            'largeImage' => $largeContract->getUrl(),
            'smallImage' => $smallContract->getUrl(),
            'alt'        => 'foo',
            'caption'    => null
        ];
    }
}
