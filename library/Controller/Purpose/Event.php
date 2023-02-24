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

       // Build the schema.org event data
        $eventData = [
        '@type'       => 'Event',
        'name'        => get_the_title($postId),
        'description' => get_the_excerpt($postId),
        'offers'      => [],
        ];

       // Event dates
        $occasions = $eventMeta['occasions_complete'][0] ?? null;
        $startDate = $occasions ? $occasions[0]['start_date'] ?? '' : '';
        $endDate = $occasions ? $occasions[0]['end_date'] ?? '' : '';

        if ($startDate) {
            $eventData['startDate'] = $startDate;
        }
        if ($endDate) {
            $eventData['endDate'] = $endDate;
        }

       // Event image
        $imageUrl = wp_get_attachment_url($eventMeta['_thumbnail_id'][0] ?? null);
        if ($imageUrl) {
            $eventData['image'] = [$imageUrl];
        }

       // Event tickets
        $bookingLink = $eventMeta['booking_link'][0] ?? null;
        if ($bookingLink) {
            $eventData['offers'][] = [
            '@type' => 'Offer',
            'name'  => 'ticket',
            'url'   => $bookingLink,
            ];
        }

        $adultPrice = $eventMeta['price_adult'][0] ?? null;
        if ($adultPrice) {
            $eventData['offers'][] = [
            '@type'         => 'Offer',
            'name'          => 'ticket',
            'price'         => $adultPrice,
            'priceCurrency' => 'SEK',
            ];
        }

       // Additional ticket types
        $additionalTypes = $eventMeta['additional_ticket_types'][0] ?? null;
        if ($additionalTypes) {
            $ticketTypes = maybe_unserialize($additionalTypes);
            foreach ($ticketTypes as $type) {
                $ticketName = $type['ticket_name'] ?? null;
                $maximumPrice = $type['maximum_price'] ?? null;
                if ($ticketName && $maximumPrice) {
                    $eventData['offers'][] = [
                    '@type'         => 'Offer',
                    'name'          => $ticketName,
                    'price'         => $maximumPrice,
                    'priceCurrency' => 'SEK',
                    ];
                }
            }
        }

       // Append the event data to the structured data
        return array_merge($eventData, $structuredData);
    }
}
