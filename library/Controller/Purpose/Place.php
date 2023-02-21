<?php

namespace Municipio\Controller\Purpose;

/**
 * Class Place
 * @package Municipio\Controller\Purpose
 */
class Place extends PurposeFactory
{
    public function __construct()
    {
        $this->key = 'place';
        $this->label = __('Place', 'municipio');

        parent::__construct($this->key, $this->label);
    }
    public function init(): void
    {
        // Append structured data for schema.org markup
        add_filter('Municipio/StructuredData', [$this, 'appendStructuredData'], 10, 3);
    }
    /**
    * Appends the structured data array (used for schema.org markup) with additional data
    *
    * @param array $structuredData The structured data to append location data to.
    * @param string $postType The post type of the post.
    * @param int $postId The ID of the post to retrieve location data for.
    *
    * @return array The updated structured data.
    *
    */
    public function appendStructuredData(array $structuredData, string $postType, int $postId): array
    {
        if (empty($postId)) {
            return $structuredData;
        }

        $locationMetaKeys = ['map', 'location']; // Post meta keys we'l check for location data.
        $additionalData = ['location' => []];

        foreach ($locationMetaKeys as $key) {
            $location = get_post_meta($postId, $key, true);
            if (empty($location)) {
                continue;
            }

            // General address
            if (!empty($location['formatted_address'])) {
                $additionalData['location'][] = [
                    '@type'   => 'Place',
                    'address' => $location['formatted_address'],
                ];
            } elseif (!empty($location['address'])) {
                $additionalData['location'][] = [
                    '@type'   => 'Place',
                    'address' => $location['address'],
                ];
            }

            // Coordinates
            if (!empty($location['lat']) && !empty($location['lng'])) {
                $additionalData['location'][] = [
                    '@type'     => 'GeoCoordinates',
                    'latitude'  => $location['lat'],
                    'longitude' => $location['lng'],
                ];
            }

            // Country
            if (!empty($location['country'])) {
                $additionalData['location'][] = [
                    '@type'          => 'PostalAddress',
                    'addressCountry' => $location['country'],
                ];
            }
        }

        return array_merge($structuredData, $additionalData);
    }
}
