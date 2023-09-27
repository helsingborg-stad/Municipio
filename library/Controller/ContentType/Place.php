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
    public function __construct()
    {
        $this->key = 'place';
        $this->label = __('Place', 'municipio');

        parent::__construct($this->key, $this->label);

        // Append location link to listing items
        add_filter('Municipio/Controller/SingularContentType/listing', [$this, 'appendListItems'], 10, 2);
    }

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
    
}
