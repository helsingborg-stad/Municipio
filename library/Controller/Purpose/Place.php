<?php

namespace Municipio\Controller\Purpose;

/**
 * Class Place
 * @package Municipio\Controller\Purpose
 */
class Place extends PurposeFactory
{
    public function init()
    {
        // Append structured data
        add_filter('Municipio/StructuredData', array($this, 'appendStructuredData'), 10, 3);
    }
    public static function getLabel(): string
    {
        return __('Place', 'municipio');
    }
    public static function getKey(): string
    {
        return 'place';
    }
    /**
    * Appends the structured data array (used for schema.org markup) with additional data
    *
    * @param array structuredData The structured data array that we're going to append to.
    * @param string postType The post type of the current page.
    * @param int postId The ID of the post you want to add structured data to.
    *
    * @return array The modified structured data array.
    */
    public function appendStructuredData(array $structuredData, string $postType, int $postId): array
    {

        if (empty($postId)) {
            return $structuredData;
        }

        $additionalData = [];

        $location = (array) get_post_meta($postId, 'map', true);

        if (!empty($location['address'])) {
            $additionalData['location'][] = [
               '@type'   => 'Place',
               'address' => $location['address'],
            ];
        }
        if (!empty($location['lat']) && !empty($location['lng'])) {
            $additionalData['location'][] = [
               '@type'     => 'GeoCoordinates',
               'latitude'  => $location['lat'],
               'longitude' => $location['lng'],
            ];
        }
        if (!empty($location['country'])) {
            $additionalData['location'][] = [
               '@type'     => 'PostalAddess',
               'country'  => $location['country'],
            ];
        }
        return array_merge($structuredData, $additionalData);
    }
}
