<?php

namespace Municipio\Controller;

use DateTime;
use Municipio\Helper\Post;

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

        $event = $this->post->getSchema();

        $mappers = [
            'description'           => new SingularEvent\Mappers\MapDescription($this->wpService),
            'priceListItems'        => new SingularEvent\Mappers\MapPriceList($this->wpService),
            'organizers'            => new SingularEvent\Mappers\MapOrganizers($this->wpService),
            'icsUrl'                => new SingularEvent\Mappers\MapIcsUrl(),
            'eventIsInThePast'      => new SingularEvent\Mappers\MapEventIsInthePast(),
            'accessibilityFeatures' => new SingularEvent\Mappers\MapPhysicalAccessibilityFeatures(),
        ];

        $this->data['placeUrl']              = $this->getPlaceUrl($event->getProperty('location'));
        $this->data['placeName']             = $this->getPlaceName($event->getProperty('location'));
        $this->data['placeAddress']          = $event->getProperty('location')['address'] ?? null;
        $this->data['eventsInTheSameSeries'] = $this->getEventsInTheSameSeries();
        $this->data['occassion']             = $this->getOccassionText($event->getProperty('startDate'), $event->getProperty('endDate'));
        $this->data['bookingLink']           = $this->post->getSchemaProperty('offers')[0]['url'] ?? null;
        $this->data['occassions']            = array_map(function ($postObject) {
            return $this->getOccassionText($postObject->getSchemaProperty('startDate'), $postObject->getSchemaProperty('endDate'));
        }, $this->data['eventsInTheSameSeries']);

        foreach ($mappers as $key => $mapper) {
            $this->data[$key] = $mapper->map($event);
        }

        $this->trySetHttpStatusHeader($this->data['eventIsInThePast']);
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
    public function getPlaceUrl(?array $places = []): string
    {
        if (empty($places)) {
            return '';
        }

        $placeName    = $places[0]['name'] ?? $places[0]['address'] ?? '';
        $placeAddress = $places[0]['address'] ?? '';

        $googleMapsUrl = 'https://www.google.com/maps/search/?api=1&query=';
        $placeLink     = $googleMapsUrl . urlencode($placeName . ', ' . $placeAddress);

        return $placeLink;
    }

    public function getPlaceName(?array $places = []): string
    {
        if (empty($places)) {
            return '';
        }

        return $places[0]['name'] ?? $places[0]['address'] ?? '';
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

        $start = ucfirst($this->wpService->dateI18n('j F Y H:i', $startDateTimestamp));
        $end   = $days > 0
            ? ucfirst($this->wpService->dateI18n('j F Y H:i', $endDateTimestamp))
            : ucfirst($this->wpService->dateI18n('H:i', $endDateTimestamp));

        return "{$start} - {$end}";
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
    private function trySetHttpStatusHeader(bool $eventIsInThePast): void
    {
        if ($eventIsInThePast) {
            $this->wpService->statusHeader(410);
        }
    }
}
