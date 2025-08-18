<?php

namespace Municipio\Controller;

use DateTime;
use Municipio\Controller\SingularEvent\Contracts\PriceListItemInterface;
use Municipio\Schema\BaseType;
use Municipio\Schema\ImageObject;
use Municipio\Schema\Place;
use Modularity\Integrations\Component\ImageResolver;
use ComponentLibrary\Integrations\Image\Image as ImageComponentContract;
use Municipio\Schema\Schema;
use Municipio\SchemaData\Utils\OpeningHoursSpecificationToString\OpeningHoursSpecificationToString;

/**
 * Controller for posts with ExhibitionEvent schema type.
 */
class SingularExhibitionEvent extends Singular
{
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
        $this->data['occassion']                     = $this->getOccassionText($event->getProperty('startDate'), $event->getProperty('endDate'));
        $this->data['physicalAccessibilityFeatures'] = $this->getPhysicalAccessibilityFeaturesList($this->post->getSchemaProperty('physicalAccessibilityFeatures'));
        $this->data['eventIsInThePast']              = $this->eventIsInThePast();
        $this->data['galleryComponentAttributes']    = $this->getGalleryComponentAttributes();
        $this->data['openingHours']                  = $this->getOpeningHours($event->getProperty('location')?->getProperty('openingHoursSpecification') ?? []);
        $this->data['specialOpeningHours']           = $this->getOpeningHours($event->getProperty('location')?->getProperty('specialOpeningHoursSpecification') ?? []);

        $this->trySetHttpStatusHeader($event);
    }

    /**
     * Get the name of the place from the Place schema.
     *
     * @param Place|null $place
     * @return string|null
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
     *
     * @param Place|null $place
     * @return string|null
     */
    private function getPlaceAddress(?Place $place): ?string
    {
        return $place?->getProperty('geo')?->getProperty('address') ?? null;
    }

    /**
     * Get the physical accessibility features from the event schema.
     *
     * @param array|null $features
     * @return string|null
     */
    private function getPhysicalAccessibilityFeaturesList(?array $features): ?string
    {
        return empty($features) ? null : implode(', ', $features);
    }

    /**
     * Get the opening hours from the event schema.
     *
     * @param array $openingHours
     * @return array|null
     */
    private function getOpeningHours(array $openingHours): ?array
    {
        if (empty($openingHours)) {
            return null;
        }

        $converter    = new OpeningHoursSpecificationToString();
        $openingHours = is_array($openingHours) ? $openingHours : [$openingHours];
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
     *
     * @return void
     */
    private function populateLanguageObject(): void
    {
        $lang                           = $this->data['lang'];
        $wp                             = $this->wpService;
        $lang->bookingTitleLabel        = $wp->__('Tickets & registration', 'municipio');
        $lang->bookingButtonLabel       = $wp->__('Go to booking page', 'municipio');
        $lang->bookingDisclaimerLabel   = $wp->__('Tickets are sold according to the reseller.', 'municipio');
        $lang->placeTitle               = $wp->__('Place', 'municipio');
        $lang->expiredEventNoticeLabel  = $wp->__('This event has already taken place.', 'municipio');
        $lang->dateLabel                = $wp->__('Date', 'municipio');
        $lang->openingHoursLabel        = $wp->__('Opening hours', 'municipio');
        $lang->specialOpeningHoursLabel = $wp->__('Special opening hours', 'municipio');
        $lang->entranceLabel            = $wp->__('Entrance', 'municipio');
        $lang->accessibilityLabel       = $wp->__('Accessibility', 'municipio');
        $lang->findUsLabel              = $wp->__('Find us', 'municipio');
        $lang->galleryLabel             = $wp->__('Gallery', 'municipio');
    }

    /**
     * Get the URL of the place from the Place schema.
     *
     * @param Place|null $place
     * @return string|null
     */
    public function getPlaceUrl(?Place $place = null): ?string
    {
        if (!$place) {
            return null;
        }
        $googleMapsUrl = 'https://www.google.com/maps/search/?api=1&query=';
        return $googleMapsUrl . urlencode($place['geo']['address']);
    }

    /**
     * Get the occasion text from the event schema.
     *
     * @param DateTime|null $startDate
     * @param DateTime|null $endDate
     * @return string
     */
    public function getOccassionText(?DateTime $startDate, ?DateTime $endDate): string
    {
        if (!$startDate || !$endDate) {
            return '';
        }

        $startDateTimestamp = $startDate->getTimestamp();
        $endDateTimestamp   = $endDate->getTimestamp();

        $start = ucfirst($this->wpService->dateI18n('j M', $startDateTimestamp));
        $end   = ucfirst($this->wpService->dateI18n('j M Y', $endDateTimestamp));

        return "{$start} - {$end}";
    }

    /**
     * Get the price list from the event schema.
     *
     * @return array
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
     *
     * @param array $offer
     * @return PriceListItemInterface
     */
    public function getPriceListItemFromOffer(array $offer): PriceListItemInterface
    {
        $priceSpecification = $offer['priceSpecification'] ?? [];
        $name               = $offer['name'] ?? '';
        $currency           = $offer['priceCurrency'] ?? '';

        $price = $this->resolvePrice($offer, $priceSpecification, $currency);

        return new \Municipio\Controller\SingularEvent\PriceListItem($name, $price);
    }

    /**
     * Resolve the price from an offer.
     *
     * @param array $offer
     * @param array $priceSpecification
     * @param string $currency
     * @return string
     */
    private function resolvePrice(array $offer, array $priceSpecification, string $currency): string
    {
        if (isset($priceSpecification['minPrice'], $priceSpecification['maxPrice'])) {
            if ($priceSpecification['minPrice'] === $priceSpecification['maxPrice']) {
                return $priceSpecification['minPrice'] . ' ' . $currency;
            }
            return $priceSpecification['minPrice'] . ' - ' . $priceSpecification['maxPrice'] . ' ' . $currency;
        }
        if (isset($priceSpecification['price'])) {
            return $priceSpecification['price'] . ' ' . $currency;
        }
        if (isset($offer['price'])) {
            return $offer['price'] . ' ' . $currency;
        }
        return $this->wpService->__('Price not available', 'municipio');
    }

    /**
     * Check if the event is in the past.
     *
     * @return bool
     */
    public function eventIsInThePast(): bool
    {
        $event   = $this->post->getSchema();
        $endDate = $event->getProperty('endDate');
        return $endDate instanceof DateTime && $endDate->getTimestamp() < time();
    }

    /**
     * Try to set the HTTP status header based on the event's status.
     *
     * @param BaseType $event
     * @return void
     */
    private function trySetHttpStatusHeader(BaseType $event): void
    {
        if ($this->eventIsInThePast()) {
            $this->wpService->statusHeader(410);
        }
    }

    /**
     * Get the gallery component attributes from the event schema.
     *
     * @return array|null
     */
    private function getGalleryComponentAttributes(): ?array
    {
        $imageProperty = $this->post->getSchemaProperty('image');
        if (is_array($imageProperty)) {
            return [
                'list' => array_map(fn($image) => $this->getImageAttributes($image), $imageProperty)
            ];
        }
        return null;
    }

    /**
     * Get the image attributes from an ImageObject.
     *
     * @param ImageObject $image
     * @return array
     */
    private function getImageAttributes(ImageObject $image): array
    {
        $smallContract = ImageComponentContract::factory($image->getProperty('@id'), [450, 280], new ImageResolver());
        $largeContract = ImageComponentContract::factory($image->getProperty('@id'), [768, 432], new ImageResolver());

        return [
            'largeImage' => $largeContract->getUrl(),
            'smallImage' => $smallContract->getUrl(),
            'alt'        => 'foo',
            'caption'    => null
        ];
    }
}
