<?php

namespace Municipio\Controller;

use Municipio\Controller\SingularEvent\Contracts\PriceListItemInterface;
use Municipio\Controller\SingularEvent\PriceListItem;
use Municipio\Helper\Post;
use Municipio\Schema\BaseType;
use Municipio\Schema\Contracts\EventContract;

/**
 * Class SingularEvent
 */
class SingularEvent extends \Municipio\Controller\Singular
{
    protected object $postMeta;
    public string $view = 'single-schema-event';

    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();

        $this->populateLanguageObject();

        $this->data['placeUrl']                      = $this->post->getSchemaProperty('location') ? $this->getPlaceUrl($this->post->getSchemaProperty('location')) : null;
        $this->data['placeName']                     = $this->post->getSchemaProperty('location') ? $this->post->getSchemaProperty('location')['name'] ?? null : null;
        $this->data['placeAddress']                  = $this->post->getSchemaProperty('location') ? $this->post->getSchemaProperty('location')['address'] : null;
        $this->data['priceListItems']                = $this->getPriceList();
        $this->data['icsDownloadLink']               = $this->getIcsDownloadLink();
        $this->data['eventsInTheSameSeries']         = $this->getEventsInTheSameSeries();
        $this->data['occassion']                     = $this->getOccassionText();
        $this->data['bookingLink']                   = $this->post->getSchemaProperty('offers')[0]['url'] ?? null;
        $this->data['organizers']                    = $this->post->getSchemaProperty('organizer') ?? [];
        $this->data['organizers']                    = !is_array($this->data['organizers']) ? [$this->data['organizers']] : $this->data['organizers'];
        $this->data['physicalAccessibilityFeatures'] = $this->post->getSchemaProperty('physicalAccessibilityFeatures') ?? null;
        $this->data['eventIsInThePast']              = $this->eventIsInThePast();
        $this->data['occassions']                    = array_map(function ($postObject) {
            return $this->getOccassionText();
        }, $this->data['eventsInTheSameSeries']);

        $this->trySetHttpStatusHeader();
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
     * @return array
     */
    public function getPlaceUrl(array $place): string
    {
        $placeName    = $place['name'] ?? '';
        $placeAddress = $place['address'] ?? '';

        $googleMapsUrl = 'https://www.google.com/maps/search/?api=1&query=';
        $placeLink     = $googleMapsUrl . urlencode($placeName . ', ' . $placeAddress);

        return $placeLink;
    }

    /**
     * Get date text
     *
     * @return string
     */
    public function getOccassionText(): string
    {
        $startDate = $this->post->getSchemaProperty('startDate');
        $endDate   = $this->post->getSchemaProperty('endDate');

        if (!$startDate || !$endDate) {
            return '';
        }

        $duration = strtotime($endDate) - strtotime($startDate);
        $days     = floor($duration / 86400);

        $start = ucfirst($this->wpService->dateI18n('j F Y H:i', strtotime($startDate)));
        $end   = $days > 0
            ? ucfirst($this->wpService->dateI18n('j F Y H:i', strtotime($endDate)))
            : ucfirst($this->wpService->dateI18n('H:i', strtotime($endDate)));

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
        } else {
            $price = $this->wpService->__('Price not available', 'municipio');
        }

        return new PriceListItem($name, $price);
    }

    /**
     * Get ICS download link
     *
     * @return string
     */
    private function getIcsDownloadLink(): string
    {
        $startDate = $this->post->getSchemaProperty('startDate');
        $endDate   = $this->post->getSchemaProperty('endDate');
        $name      = $this->post->getSchemaProperty('name');

        if (!$startDate || !$endDate || !$name) {
            return '';
        }

        $icsData = [
        'BEGIN:VCALENDAR',
        'VERSION:2.0',
        'BEGIN:VEVENT',
        'DTSTART:' . date('Ymd\THis\Z', strtotime($startDate)),
        'DTEND:' . date('Ymd\THis\Z', strtotime($endDate)),
        'SUMMARY:' . $name,
        'END:VEVENT',
        'END:VCALENDAR',
        ];

        $icsData = implode("\n", $icsData);

        return $icsData = 'data:text/calendar;charset=utf8,' . $icsData;
    }

    /**
     * Get events in the same series
     *
     * @return array
     */
    private function getEventsInTheSameSeries(): array
    {
        if (empty($this->post->getSchemaProperty('eventsInSameSeries'))) {
            return [];
        }

        $eventIds = array_map(fn($eventInSerie) => $eventInSerie['@id'], $this->post->getSchemaProperty('eventsInSameSeries'));

        $posts = $this->wpService->getPosts([
        'post_type'    => $this->post->getPostType(),
        'meta_query'   => [
            [
                'key'     => 'originId',
                'value'   => $eventIds,
                'compare' => 'IN'
            ],
        ],
        'post__not_in' => [$this->data['post']->getId()],
        ]);

        return array_map(fn($post) => Post::preparePostObject($post), $posts);
    }

    /**
     * Try to set HTTP status header
     * If the event is in the past, set 410 Gone
     */
    private function trySetHttpStatusHeader(): void
    {
        if ($this->eventIsInThePast()) {
            $this->wpService->statusHeader(410);
        }
    }

    /**
     * Check if the event is in the past
     */
    private function eventIsInThePast(): bool
    {
        $startDate = $this->post->getSchemaProperty('endDate');
        return strtotime($startDate) < time();
    }
}
