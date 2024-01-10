<?php

namespace Municipio\Controller\ContentType;

use Municipio\Helper\ContentType as ContentTypeHelper;

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
        $this->key   = 'place';
        $this->label = __('Place', 'municipio');

        parent::__construct($this->key, $this->label);
    }

    public function addHooks(): void
    {
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

    public function getStructuredData(int $postId): array
    {

        $locationMetaKeys = ['map', 'location']; // Post meta keys we'l check for location data.
        $structuredData   = [];

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
