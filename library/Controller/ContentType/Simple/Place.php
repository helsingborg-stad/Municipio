<?php

namespace Municipio\Controller\ContentType\Simple;

/**
 * Class Place
 *
 * Used to represent physical places.
 *
 * @package Municipio\Controller\ContentType
 */
class Place extends \Municipio\Controller\ContentType\ContentTypeFactory
{
    /**
     * Constructor method to set key and label for the Place content type.
     */
    public function __construct()
    {
        $this->key   = 'Place';
        $this->label = __('Place', 'municipio');

        parent::__construct($this->key, $this->label);
    }

    /**
     * Add hooks for the Place content type.
     *
     * @return void
     */
    public function addHooks(): void
    {
        // Append location link to listing items
        add_filter('Municipio/Controller/SingularContentType/listing', [$this, 'appendListItems'], 10, 2);
    }
    /**
     * Append location-related list items to the listing array.
     *
     * @param array $listing The existing listing array.
     * @param array $fields The fields associated with the post.
     *
     * @return array The updated listing array.
     */
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

    /**
     * Build a Google Maps link based on location coordinates.
     *
     * @param array $location An array containing latitude and longitude information.
     *
     * @return string|bool The generated Google Maps link or false if location information is missing.
     */
    public function buildGoogleMapsLink(array $location = [])
    {
        if (empty($location) || empty($location['lng']) || empty($location['lat'])) {
            return false;
        }
        return
        'https://www.google.com/maps/dir/?api=1&destination='
            . $location['lat']
            . ','
            . $location['lng']
            . '&travelmode=transit';
    }
}
