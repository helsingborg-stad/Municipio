<?php

namespace Municipio\Controller;

use DateTime;
use Municipio\Controller\SingularEvent\Contracts\PriceListItemInterface;
use Municipio\ImageConvert\Contract\ImageContract;
use Municipio\ImageConvert\Contract\ImageContractInterface;
use Municipio\Schema\BaseType;
use Municipio\Schema\ImageObject;
use Municipio\Schema\Place;
use Modularity\Integrations\Component\ImageResolver;
use ComponentLibrary\Integrations\Image\Image as ImageComponentContract;
use Municipio\Schema\Event;
use Municipio\Schema\ExhibitionEvent;
use Municipio\Schema\Schema;
use Municipio\SchemaData\Utils\OpeningHoursSpecificationToString\OpeningHoursSpecificationToString;

/**
 * Class SingularExhibitionEvent
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

        $this->data['displayFeaturedImage']             = false;
        $this->data['placeUrl']                         = $this->getPlaceUrl($event->getProperty('location'));
        $this->data['placeName']                        = $event->getProperty('location')['name'] ?? $event->getProperty('location')['address'] ?? null;
        $this->data['placeAddress']                     = $event->getProperty('location')['address'] ?? null;
        $this->data['priceListItems']                   = $this->getPriceList();
        $this->data['occassion']                        = $this->getOccassionText($event->getProperty('startDate'), $event->getProperty('endDate'));
        $this->data['bookingLink']                      = $this->post->getSchemaProperty('offers')[0]['url'] ?? null;
        $this->data['organizers']                       = $this->post->getSchemaProperty('organizer') ?? [];
        $this->data['organizers']                       = !is_array($this->data['organizers']) ? [$this->data['organizers']] : $this->data['organizers'];
        $this->data['physicalAccessibilityFeatures']    = $this->post->getSchemaProperty('physicalAccessibilityFeatures') ?? null;
        $this->data['eventIsInThePast']                 = $this->eventIsInThePast();
        $this->data['openingHoursSpecification']        = $event->getProperty('openingHoursSpecification') ?? [];
        $this->data['specialOpeningHoursSpecification'] = $event->getProperty('specialOpeningHoursSpecification') ?? [];
        $this->data['galleryComponentAttributes']       = $this->getGalleryComponentAttributes();
        $this->data['openingHours']                     = $this->getOpeningHours($event->getProperty('location')?->getProperty('openingHoursSpecification') ?? []);
        $this->data['specialOpeningHours']              = $this->getOpeningHours($event->getProperty('location')?->getProperty('specialOpeningHoursSpecification') ?? []);

        $this->trySetHttpStatusHeader($event);
    }

    private function getOpeningHours(array $openingHours): ?array
    {
        if (empty($openingHours)) {
            return null;
        }

        $converter    = new OpeningHoursSpecificationToString();
        $openingHours = is_array($openingHours) ? $openingHours : [$openingHours];
        $openingHours = array_map(
            function ($item) {
                return Schema::openingHoursSpecification()
                    ->setProperty('name', $item['name'] ?? null)
                    ->setProperty('dayOfWeek', $item['dayOfWeek'] ?? null)
                    ->setProperty('opens', $item['opens'] ?? null)
                    ->setProperty('closes', $item['closes'] ?? null);
            },
            $openingHours
        );

        $formatted = array_map(fn($spec) => $converter->convert($spec), $openingHours);
        $flattened = array_merge(...$formatted);

        return $flattened;
    }

    /**
     * Populate the language object.
     */
    private function populateLanguageObject(): void
    {
        $this->data['lang']->description        = $this->wpService->__('Description', 'municipio');
        $this->data['lang']->addToCalendar      = $this->wpService->__('Add to calendar', 'municipio');
        $this->data['lang']->bookingTitle       = $this->wpService->__('Tickets & registration', 'municipio');
        $this->data['lang']->bookingButton      = $this->wpService->__('Go to booking page', 'municipio');
        $this->data['lang']->bookingDisclaimer  = $this->wpService->__('Tickets are sold according to the reseller.', 'municipio');
        $this->data['lang']->occassionsTitle    = $this->wpService->__('Date and time', 'municipio');
        $this->data['lang']->moreOccassions     = $this->wpService->__('Other occassions', 'municipio');
        $this->data['lang']->placeTitle         = $this->wpService->__('Place', 'municipio');
        $this->data['lang']->priceTitle         = $this->wpService->__('Price', 'municipio');
        $this->data['lang']->organizersTitle    = $this->wpService->__('Organizers', 'municipio');
        $this->data['lang']->accessibilityTitle = $this->wpService->__('Accessibility', 'municipio');
        $this->data['lang']->expiredEventNotice = $this->wpService->__('This event has already taken place.', 'municipio');
    }

    /**
     * Get place link attributes
     *
     * @return string
     */
    public function getPlaceUrl(?Place $place = null): string
    {
        if (!$place) {
            return '';
        }

        $placeName    = $place['name'] ?? $place['address'] ?? '';
        $placeAddress = $place['address'] ?? '';

        $googleMapsUrl = 'https://www.google.com/maps/search/?api=1&query=';
        $placeLink     = $googleMapsUrl . urlencode($placeName . ', ' . $placeAddress);

        return $placeLink;
    }

    /**
     * Get date text
     *
     * @param DateTime|null $startDate
     * @param DateTime|null $endDate
     * @return string
     */
    public function getOccassionText(DateTime|null $startDate, DateTime|null $endDate): string
    {
        if (!$startDate || !$endDate) {
            return '';
        }

        $startDateTimestamp = $startDate->getTimestamp();
        $endDateTimestamp   = $endDate->getTimestamp();

        $duration = $endDateTimestamp - $startDateTimestamp;
        $days     = floor($duration / 86400);

        $start = ucfirst($this->wpService->dateI18n('j M', $startDateTimestamp));
        $end   = ucfirst($this->wpService->dateI18n('j M Y', $endDateTimestamp));

        return "{$start} - {$end}";
    }

    /**
     * Get price list
     *
     * @return PriceListItemInterface[]
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
     * Get price list item from offer
     *
     * @param array $offer
     * @return PriceListItemInterface
     */
    public function getPriceListItemFromOffer(array $offer): PriceListItemInterface
    {
        $priceSpecification = $offer['priceSpecification'] ?? [];
        $name               = $offer['name'] ?? '';
        $currency           = $offer['priceCurrency'] ?? '';

        if (isset($priceSpecification['minPrice']) && isset($priceSpecification['maxPrice'])) {
            if ($priceSpecification['minPrice'] === $priceSpecification['maxPrice']) {
                $price = $priceSpecification['minPrice'] . ' ' . $currency;
            } else {
                $price = $priceSpecification['minPrice'] . ' - ' . $priceSpecification['maxPrice'] . ' ' . $currency;
            }
        } elseif (isset($priceSpecification['price'])) {
            $price = $priceSpecification['price'] . ' ' . $currency;
        } elseif (isset($offer['price'])) {
            $price = $offer['price'] . ' ' . $currency;
        } else {
            $price = $this->wpService->__('Price not available', 'municipio');
        }

        // Assuming PriceListItemInterface is implemented by a class, e.g., PriceListItem
        return new \Municipio\Controller\SingularEvent\PriceListItem($name, $price);
    }

    /**
     * Determine if the event is in the past.
     *
     * @return bool
     */
    public function eventIsInThePast(): bool
    {
        $event   = $this->post->getSchema();
        $endDate = $event->getProperty('endDate');

        if ($endDate instanceof \DateTime) {
            return $endDate->getTimestamp() < time();
        }

        return false;
    }

    /**
     * Try to set HTTP status header
     * If the event is in the past, set 410 Gone
     */
    private function trySetHttpStatusHeader(BaseType $event): void
    {
        if ($this->eventIsInThePast($event->getProperty('startDate'))) {
            $this->wpService->statusHeader(410);
        }
    }

    /**
     * Get gallery component attributes from the event schema image property if the image property is an array of ImageObjects.
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
     * Get image attributes for the gallery component.
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
