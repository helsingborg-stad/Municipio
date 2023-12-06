<?php

namespace Municipio\Controller\ContentType;

/**
 * Class Place
 *
 * Used to represent physical places such as buildings, parks, etc.
 *
 * @package Municipio\Controller\ContentType
 */
class Place extends ContentTypeFactory
{

    public $secondaryContentType = [];
    
    public function __construct()
    {
        $this->key = 'place';
        $this->label = __('Place', 'municipio');

        parent::__construct($this->key, $this->label);

    }

    public function init(): void
    {  
        $this->addHooks();
    }

    public function addHooks(): void {
        // Append structured data for schema.org markup
        // add_filter('Municipio/StructuredData', [$this, 'appendStructuredData'], 10, 3);
        // Append location link to listing items
        add_filter('Municipio/Controller/SingularContentType/listing', [$this, 'appendListItems'], 10, 2);
    }

    
    // TODO - Move to a more appropriate place
    public function appendListItems($listing, $fields)
    {
        // Street name linked to Google Maps
        if (!empty($fields['location'])) {
            if (!empty($fields['location']['street_name']) && !empty($fields['location']['street_number'])) {
                $locationLabel = $fields['location']['street_name'] . ' ' . $fields['location']['street_number'];
            } elseif (!empty($fields['location']['name'])) {
                $locationLabel = $fields['location']['name'];
            } else {
                $locationLabel = $fields['location']['address'];
            }
            $locationLink = $this->buildGoogleMapsLink($fields['location']);
            if ($locationLink) {
                $listing['location'] = \Municipio\Helper\Listing::createListingItem(
                    $locationLabel,
                    $locationLink,
                    ['src' => 'directions_bus']
                );
            }
        }
        return $listing;
    }
    public function buildGoogleMapsLink(array $location = [])
    {
        if (empty($location) || empty($location['lng']) || empty($location['lat'])) {
            return false;
        }
        return 'https://www.google.com/maps/dir/?api=1&destination=' . $location['lat'] . ',' . $location['lng'] . '&travelmode=transit';
    }
    /**
    * Appends the structured data array (used for schema.org markup) with additional data
    *
    * @param array $structuredData The structured data to append location data to.
    * @param int $postId The ID of the post to retrieve location data for.
    *
    * @return array The updated structured data.
    *
    */
    public function appendStructuredData(int $postId): array
    {
        if (empty($postId)) {
            return [];
        }

        $locationMetaKeys = ['map', 'location']; // Post meta keys we'l check for location data.
        
        $structuredData = ['location' => []];

        foreach ($locationMetaKeys as $key) {
            $location = get_post_meta($postId, $key, true);
            if (empty($location)) {
                continue;
            }

            // General address
            if (!empty($location['formatted_address'])) {
                $structuredData['location'][] = [
                    '@type'   => 'Place',
                    'address' => $location['formatted_address'],
                ];
            } elseif (!empty($location['address'])) {
                $structuredData['location'][] = [
                    '@type'   => 'Place',
                    'address' => $location['address'],
                ];
            }

            // Coordinates
            if (!empty($location['lat']) && !empty($location['lng'])) {
                $structuredData['location'][] = [
                    '@type'     => 'GeoCoordinates',
                    'latitude'  => $location['lat'],
                    'longitude' => $location['lng'],
                ];
            }

            // Country
            if (!empty($location['country'])) {
                $structuredData['location'][] = [
                    '@type'          => 'PostalAddress',
                    'addressCountry' => $location['country'],
                ];
            }
        }

        return $structuredData;
    }
}