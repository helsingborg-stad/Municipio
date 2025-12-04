<?php

namespace Municipio\Controller;

use Municipio\Helper\Listing;

/**
 * Class SingularPlace
 *
 * Used to represent physical places.
 */
class SingularPlace extends \Municipio\Controller\Singular
{
    public string $view = 'single-schema-place';

    public function init()
    {
        parent::init();

        $pageID                     = $this->getPageID();
        $this->data['relatedPosts'] = $this->getRelatedPosts($pageID);

        $this->addHooks();
    }

    /**
     * Add hooks for the Place content type.
     *
     * @return void
     */
    public function addHooks(): void
    {
        // Append location link to listing items
        add_filter('Municipio/Controller/SingularPlace/listing', [$this, 'appendListItems'], 10, 2);
        add_filter('Municipio/viewData', [$this, 'populatePostWithAdditionalPlaceViewData'], 10, 1);
    }

    /**
     * Populate the view data with additional information for a place post.
     *
     * @param array $data The view data to populate.
     *
     * @return array The updated view data.
     */
    public function populatePostWithAdditionalPlaceViewData($data)
    {
        $data['post'] = $this->complementPlacePost($data['post']);
        return $data;
    }

    /**
     * Prepare the query object by enhancing each post within the query result.
     *
     * @param WP_Query $query The query object to prepare.
     *
     * @return WP_Query|bool The prepared query object, or false if the input is not a valid query.
     */
    public function prepareQuery($query)
    {
        $query = parent::prepareQuery($query);

        if (empty($query)) {
            return $query;
        }

        if ($query->have_posts()) {
            foreach ($query->posts as &$post) {
                $post = $this->complementPlacePost($post);
            }
        }

        return $query;
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

    /**
     * Complement a place post with additional information.
     *
     * @param mixed $post The post object or post ID to complement.
     *
     * @return mixed The complemented post object.
     */
    private function complementPlacePost($post)
    {
        // Fetch custom fields for the post
        $fields = get_fields($post->ID ?? null);

        // Assign additional information to the post object
        $post->bookingLink = $fields['booking_link'] ?? false;
        $post->placeInfo   = $this->createPlaceInfoList($fields);

        return $post;
    }

    /**
     * Create a list of place information based on specified fields.
     *
     * @param array $fields An array of fields containing information about the place.
     *
     * @return array The list of place information.
     */
    private function createPlaceInfoList($fields)
    {
        $list = [];
        // Phone number
        if (!empty($fields['phone'])) {
            $list['phone'] = Listing::createListingItem(
                $fields['phone'],
                '',
                ['src' => 'call']
            );
        }

        // Website link (with fixed label)
        if (!empty($fields['website'])) {
            $list['website'] = Listing::createListingItem(
                __('Visit website', 'municipio'),
                $fields['website'],
                ['src' => 'language'],
            );
        }

        // Apply filters to listing items
        $list = apply_filters(
            'Municipio/Controller/SingularPlace/listing',
            $list,
            $fields
        );

        return $list;
    }
}
