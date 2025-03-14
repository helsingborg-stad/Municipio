<?php

namespace Municipio\Controller;

use Municipio\Controller\SingularEvent\Contracts\PriceListItemInterface;
use Municipio\Controller\SingularEvent\PriceListItem;
use Municipio\Helper\Post;
use Municipio\Helper\WpService;
use Spatie\SchemaOrg\BaseType;
use Spatie\SchemaOrg\Contracts\EventContract;

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

        // echo '<pre>' . print_r($this->data['post'], true) . '</pre>';
        // die();

        /* @var EventContract $schemaObject */
        $schemaObject = $this->data['post']->schemaObject;


        $location = $schemaObject->getProperty('location') ?? null;

        $this->data['placeUrl']              = $location ? $this->getPlaceUrl($location) : null;
        $this->data['placeName']             = $location ? $location['name'] ?? null : null;
        $this->data['placeAddress']          = $location ? $location['address'] : null;
        $this->data['durationText']          = $this->getDurationText($schemaObject);
        $this->data['priceListItems']        = $this->getPriceList($schemaObject);
        $this->data['icsDownloadLink']       = $this->getIcsDownloadLink($schemaObject);
        $this->data['eventsInTheSameSeries'] = $this->getEventsInTheSameSeries($schemaObject);
        $this->data['dateAndTime']           = $this->getDateAndTime($schemaObject);
        $this->data['bookingLink']           = $schemaObject->getProperty('offers')[0]['url'] ?? null;
        $this->data['organizers']            = $schemaObject->getProperty('organizer') ?? [];
        $this->data['organizers']            = !is_array($this->data['organizers']) ? [$this->data['organizers']] : $this->data['organizers'];


        $this->data['dateAndTimeForEventsInSameSeries'] = array_map(function ($postObject) {
            return $this->getDateAndTime($postObject->schemaObject);
        }, $this->data['eventsInTheSameSeries']);

        // echo '<pre>' . print_r($schemaObject, true) . '</pre>';
        // die();
    }

    /**
     * Get place link attributes
     *
     * @return array
     */
    public function getPlaceUrl(array $place): string
    {
        $googleMapsUrl = 'https://www.google.com/maps/search/?api=1&query=';
        $placeName     = $place['name'] ?? '';
        $placeAddress  = $place['address'] ?? '';

        $googleMapsUrl = 'https://www.google.com/maps/search/?api=1&query=';
        $placeLink     = $googleMapsUrl . urlencode($placeName . ', ' . $placeAddress);

        return $placeLink;
    }

    /**
     * Get date text
     *
     * @param BaseType&EventContract $event
     * @return string
     */

    public function getDurationText(BaseType&EventContract $event): string
    {
        $startDate = $event->getProperty('startDate');
        $endDate   = $event->getProperty('endDate');

        if ($startDate && $endDate) {
            $startTime = date('H:i', strtotime($startDate));
            $endTime   = date('H:i', strtotime($endDate));

            $duration = strtotime($endDate) - strtotime($startDate);
            $hours    = floor($duration / 3600);
            $minutes  = ($duration % 3600) / 60;

            return sprintf('%s-%s (%d hours %d min)', $startTime, $endTime, $hours, $minutes);
        }

        return '';
    }

    private function getDateAndTime(BaseType&EventContract $event): array
    {
        return [
            'local' => date('l j F Y', strtotime($event->getProperty('startDate'))),
            'time'  => $this->getDurationText($event),
        ];
    }

    /**
     * Get price list
     *
     * @param BaseType&EventContract $event
     * @return PriceListItemInterface[]
     */
    public function getPriceList(BaseType&EventContract $event): array
    {
        $offers = $event->getProperty('offers');

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
            $price = WpService::get()->__('Price not available', 'municipio');
        }

        return new PriceListItem($name, $price);
    }

    private function getIcsDownloadLink(BaseType&EventContract $event): string
    {
        $startDate = $event->getProperty('startDate');
        $endDate   = $event->getProperty('endDate');
        $name      = $event->getProperty('name');

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

    private function getEventsInTheSameSeries(BaseType&EventContract $event): array
    {
        $keywords = $event->getProperty('keywords');
        $commonId = null;

        foreach ($keywords as $keyword) {
            if ($keyword['inDefinedTermSet']['name'] === 'event-ids') {
                $commonId = $keyword['name'];
                break;
            }
        }

        if (!$commonId) {
            return [];
        }

        $commonIdLength = strlen($commonId);

        $posts = get_posts([
            'post_type'    => 'event',
            'meta_query'   => [
                [
                    'key'     => 'schemaData',
                    'value'   => "\"https://schema.org/EventIds\";}s:4:\"name\";s:{$commonIdLength}:\"{$commonId}\"",
                    'compare' => 'LIKE'
                ],
            ],
            'post__not_in' => [$this->data['post']->getId()],
        ]);

        return array_map(fn($post) => Post::preparePostObject($post), $posts);
    }
}
