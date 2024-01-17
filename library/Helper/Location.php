<?php

namespace Municipio\Helper;

/**
 * Class Location
 */
class Location
{
    /**
     * Add location data to the given posts.
     *
     * @param array $posts An array of post objects to add location data to.
     * @return array The modified array of post objects.
     */
    public static function addLocationDataToPosts(array $posts): array
    {
        foreach ($posts as &$_post) {
            if (empty($_post->id)) {
                continue;
            }
            $location = get_field("location", $_post->id);
            if (!empty($location["lat"]) && !empty($location["lng"])) {
                $_post->location = $location;
            }
        }
        return $posts;
    }
    /**
     * Create pins from the given posts .
     *
     * @param array $posts An array of post objects to create pins from .
     *
     * @return array $pins An array of pins .
     */
    public static function createMapMarkers(array $posts): array
    {
        $pins = [];

        if (!empty($posts)) {
            foreach ($posts as $post) {
                $pin = self::createMapMarker($post);
                if (!empty($pin)) {
                    $pins[] = $pin;
                }
            }
        }

        return $pins;
    }

    /**
     * Create a pin from the given post.
     *
     * @param WP_Post $post The post to create pin data from.
     *
     * @return array $pin The pin data.
     */
    public static function createMapMarker(\WP_Post $post): array
    {
        $pin = [];
        if (!empty($post->location['lat']) && !empty($post->location['lng'])) {
            $permalink = get_permalink($post->id);

            $pin = [
                'lat'     => $post->location['lat'],
                'lng'     => $post->location['lng'],
                'tooltip' => [
                    'title'      => $post->post_title ?? '',
                    'excerpt'    => $post->post_excerpt ?? '',
                    'url'        => $permalink,
                    'directions' => [
                        'url'   => 'https://www.google.com/maps/dir/?api=1&destination=' .
                        $post->location['lat'] . ',' . $post->location['lng'] . '&travelmode=transit',
                        'label' => __('Get directions on Google Maps', 'municipio'),
                    ]
                ],
            ];

            // Add icon to pin
            if (!empty($post->termIcon)) {
                $pin['icon'] = $post->termIcon;
            }
        }

        return $pin;
    }
    /**
     * Filter out posts that have location data.
     *
     * @param array $posts An array of post objects to filter.
     *
     * @return array An array of post objects with location data.
     */
    public static function filterPostsWithLocationData(array $posts): array
    {
        $postsWithLocation = [];

        if (!empty($posts)) {
            foreach ($posts as $post) {
                if (!empty($post->location['lat']) && !empty($post->location['lng'])) {
                    $postsWithLocation[] = $post;
                }
            }
        }

        return $postsWithLocation;
    }
}
