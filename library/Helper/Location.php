<?php

namespace Municipio\Helper;

class Location
{
    /**
     * Add location data to the given posts.
     *
     * @param array $posts An array of post objects to add location data to.
     *
     * @return array The modified array of post objects.
     */
    public static function addLocationDataToPosts(array $posts): array
    {
        foreach ($posts as &$_post) {
            $location = get_field("location", $_post->id);
            if (!empty($location) && !empty($location["lat"]) && !empty($location["lng"])) {
                $_post->location = $location;
            }
        }
        return $posts;
    }
    /**
     * Create pin data for the given posts.
     *
     * @param array $posts An array of post objects to create pin data for.
     * @param bool $displayGoogleMapsLink Whether to include a Google Maps link in the tooltip.
     *
     * @return array An array of pin data.
     */
    public static function createPinDataForPosts(array $posts, bool $displayGoogleMapsLink): array
    {
        $pins = [];

        if (!empty($posts)) {
            foreach ($posts as $post) {
                if (!empty($post->location['lat']) && !empty($post->location['lng'])) {
                    $pin = [
                        'lat' => $post->location['lat'],
                        'lng' => $post->location['lng'],
                        'tooltip' => [
                            'title' => $post->postTitle ?? '',
                            'content' => $post->postExcerpt ?? '',
                        ],
                    ];

                    if ($displayGoogleMapsLink) {
                        $pin['tooltip']['direction'] = [
                            'url' => '#',
                            'label' => __('Get directions on Google Maps', 'municipio'),
                        ];
                    }

                    $pins[] = $pin;
                }
            }
        }

        return $pins;
    }
    /**
     * Filter posts that have location data.
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
