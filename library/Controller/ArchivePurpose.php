<?php

namespace Municipio\Controller;

class ArchivePurpose extends \Municipio\Controller\Archive
{
    /**
     * Initializes the archive purpose controller.
     *
     * This method sets a flag to display the OpenStreetMap on archive pages that have the purpose "place"
     * or a secondary purpose of "place". It also registers a filter to add location data to the archive posts.
     */
    public function init()
    {

        add_filter('Municipio/Controller/Archive/getArchivePosts', [$this, 'addLocationToArchivePosts'], 10, 1);

        parent::init();

        $this->data['displayArchiveLoop'] = (bool) ($this->data['archiveProps']->displayArchiveLoop ?? true);

        if (\Municipio\Helper\Purpose::hasPurpose('place', get_post_type(), true)) {
            $this->data['displayOpenstreetmap'] = (bool) ($this->data['archiveProps']->displayOpenstreetmap ?? false);

            if ($this->data['displayOpenstreetmap']) {
                $this->data['displayGoogleMapsLink'] = (bool) ($this->data['archiveProps']->displayGoogleMapsLink
                ?? true);
                $this->data['pins'] = $this->setupPins($this->data['posts']);
                $this->data['postsWithLocation'] = $this->setupPostsWithLocation($this->data['posts']);
            }
        }
    }
    private function setupPins(array $posts = []): array
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

                    if ($this->data['displayGoogleMapsLink']) {
                    // TODO Change "direction" to "directions" once the component has been updated
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
    private function setupPostsWithLocation(array $posts = []): array
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


    /**
     * Adds location data to the archive posts.
     *
     * @param array $posts An array of post objects to add location data to.
     *
     * @return array The modified array of post objects.
     */
    public function addLocationToArchivePosts($posts)
    {
        foreach ($posts as &$_post) {
            $location = get_field("location", $_post->id);
            if (!empty($location) && !empty($location["lat"]) && !empty($location["lng"])) {
                $_post->location = $location;
            }
        }
        return $posts;
    }
}
