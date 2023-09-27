<?php

namespace Municipio\Controller\ContentType;

use Municipio\Helper\ContentType as ContentTypeHelper;

/**
 * Class Event
 *
 * Used to represent events such as concerts, exhibitions, etc.
 *
 * @package Municipio\Controller\ContentType
 */
class Event extends ContentTypeFactory implements ContentTypeComplexInterface
{
    public function __construct()
    {
        $this->key = 'event';
        $this->label = __('Event', 'municipio');

        parent::__construct($this->key, $this->label);

        $this->addSecondaryContentType(new Place());

    }
    /**
     * addSecondaryContentType
     *
     * @param ContentTypeComponentInterface $contentType
     * @return void
     */
    public function addSecondaryContentType(ContentTypeComponentInterface $contentType): void
    {
        if (ContentTypeHelper::validateSimpleContentType($contentType, $this)) {
            $this->secondaryContentType[] = $contentType;
        }
    }
    /**
     * Appends the structured data array (used for schema.org markup) with additional data for events.
     * Will be printed in the head of the html document.
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

        $post = \Municipio\Helper\Post::preparePostObject(get_post($postId));

       // Build the schema.org event data
        $eventData = [
        '@type'       => 'Event',
        'name'        => $post->postTitle,
        'description' => $post->postExcerpt,
        'offers'      => [],
        ];

        $eventMeta = get_post_meta($postId);

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
