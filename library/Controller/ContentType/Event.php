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
    /**
     * Constructor method the Event content type.
     */
    public function __construct()
    {
        $this->key   = 'event';
        $this->label = __('Event', 'municipio');

        $this->addSecondaryContentType(new Place());

        $this->schemaParams = $this->applySchemaParamsFilter();

        parent::__construct($this->key, $this->label);
    }
    /**
     * Add hooks for the Event content type.
     */
    public function addHooks(): void
    {
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
     * Returns an array of schema parameters for the Event content type.
     *
     * @return array The schema parameters.
     */
    protected function schemaParams(): array
    {
        $params = [
            'startDate' => [
                'schemaType' => 'Date',
                'label'      => __('Start date', 'municipio')
            ],
            'endDate'   => [
                'schemaType' => 'Date',
                'label'      => __('End date', 'municipio')
            ],
            'image'     => [
                'schemaType' => 'ImageObject',
                'label'      => __('Image', 'municipio')
            ],
            'offers'    => [
                'schemaType' => 'Offer',
                'label'      => __('Offers', 'municipio')
            ],
        ];
        foreach ($this->getSecondaryContentType() as $contentType) {
            switch ($contentType->getKey()) {
                case 'place':
                    $placeParams        = $contentType->getSchemaParams();
                    $params['location'] = $placeParams['geo'];
                    break;

                default:
                    break;
            }
        }

        return $params;
    }

    /**
     * Get the structured data for a legacy event.
     *
     * @param int $postId The ID of the event post.
     * @return array The structured data array.
     */
    public function legacyGetStructuredData(int $postId): array
    {

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
        $endDate   = $occasions ? $occasions[0]['end_date'] ?? '' : '';

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
                $ticketName   = $type['ticket_name'] ?? null;
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

        if (empty($eventData['offers'])) {
            unset($eventData['offers']);
        }
       // Append the event data to the structured data
        return $eventData;
    }
}
