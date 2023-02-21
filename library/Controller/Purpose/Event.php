<?php

namespace Municipio\Controller\Purpose;

/**
 * Class Event
 * @package Municipio\Controller\Purpose
 */
class Event extends PurposeFactory
{
    public function __construct()
    {
        $this->key = 'event';
        $this->label = __('Event', 'municipio');

        parent::__construct($this->key, $this->label, ['place' => Place::class]);
    }

    public function init(): void
    {
        // Append structured data for schema.org markup
        add_filter('Municipio/StructuredData', [$this, 'appendStructuredData'], 10, 3);
    }
/**
 * Appends the structured data array (used for schema.org markup) with additional data for events.
 *
 * @param array $structuredData The structured data to append event data to.
 * @param string $postType The post type of the post.
 * @param int $postId The ID of the post to retrieve event data for.
 *
 * @return array The updated structured data.
 */
    public function appendStructuredData(array $structuredData, string $postType, int $postId): array
    {
        if (empty($postId)) {
            return $structuredData;
        }

        // Retrieve the event post meta data
        $eventMeta = get_post_meta($postId);

        // Event dates
        $startDate = '';
        $endDate = '';

        if (isset($eventMeta['occasions_complete']) && is_array($eventMeta['occasions_complete'])) {
            $occasions = maybe_unserialize($eventMeta['occasions_complete'][0]);
            if (isset($occasions[0]['start_date'])) {
                $startDate = $occasions[0]['start_date'];
            }
            if (isset($occasions[0]['end_date'])) {
                $endDate = $occasions[0]['end_date'];
            }
        }

        $thumbId = isset($eventMeta['_thumbnail_id'][0]) ? $eventMeta['_thumbnail_id'][0] : false;
        $imageUrl = $thumbId ? wp_get_attachment_url($thumbId) : '';

        // Build the schema.org event data
        $eventData = [
            '@type'       => 'Event',
            'name'        => get_the_title($postId),
            'startDate'   => $startDate,
            'endDate'     => $endDate,
            'description' => get_the_excerpt($postId),
            'image'       => $imageUrl,
            'offers'      => [],
        ];

        // Event tickets
        if (isset($eventMeta['booking_link']) && isset($eventMeta['booking_link'][0])) {
            array_push($eventData['offers'], [
                '@type' => 'Offer',
                'name'  => 'ticket',
                'url'   => $eventMeta['booking_link'][0],
            ]);
        }
        if (isset($eventMeta['price_adult']) && isset($eventMeta['price_adult'][0])) {
            array_push($eventData['offers'], [
                '@type' => 'Offer',
                'name'  => 'ticket',
                'price'   => $eventMeta['price_adult'][0],
                'priceCurrency' => 'SEK',
            ]);
        }

        // TODO Add any other structures for tickets that may be available on events
        if (isset($eventMeta['additional_ticket_types']) && is_array($eventMeta['additional_ticket_types']) && isset($eventMeta['additional_ticket_types'][0])) {
            $ticketTypes = maybe_unserialize($eventMeta['additional_ticket_types'][0]);
            foreach ($ticketTypes as $type) {
                if (isset($type['ticket_name']) && isset($type['maximum_price'])) {
                    array_push($eventData['offers'], [
                        '@type'         => 'Offer',
                        'name'          => $type['ticket_name'],
                        'price'         => $type['maximum_price'],
                        'priceCurrency' => 'SEK',
                    ]);
                }
            }
        }
        // Append the event data to the structured data
        return array_merge($eventData, $structuredData);
    }
}
