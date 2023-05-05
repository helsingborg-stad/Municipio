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
        parent::init();

        add_filter('Municipio/Controller/Archive/getArchivePosts', [$this, 'addLocationToArchivePosts'], 10, 1);

        // TODO: Add setting for this in the customizer:
        $this->data['displayArchiveLoop'] = (bool) ($this->data['archiveProps']->displayArchiveLoop ?? true);

        if (\Municipio\Helper\Purpose::hasPurpose('place', get_post_type(), true)) {
            $this->data['displayOpenstreetmap'] = (bool) ($this->data['archiveProps']->displayOpenstreetmap ?? false);

            // TODO: Add setting for this in the customizer:
            $this->data['displayGoogleMapsLink'] = (bool) ($this->data['archiveProps']->displayGoogleMapsLink ?? true);

            if ($this->data['displayOpenstreetmap']) {
                $this->setupMapPins();
            }
        }
    }

    private function setupMapPins()
    {
        $this->data['pins'] = [];
        $this->data['postsWithLocation'] = [];

        $i = 0;

        foreach ($this->data['posts'] as $key => $post) {
            if (!empty($post->location) && !empty($post->location['lat']) && !empty($post->location['lng'])) {
                $i++;
                $this->data['postsWithLocation'][] = $post;
                $this->data['pins'][$i] = [
                    'lat' => $post->location['lat'],
                    'lng' => $post->location['lng'],
                    'tooltip' => [
                        'title' => $post->postTitle,
                        'content' => $post->postExcerpt,
                    ],
                ];
                if ($this->data['displayGoogleMapsLink']) {
                    // TODO: Update this to 'directions' when the new version of the OpenStreetMap component is pushed. Also check for 'displayGoogleMapsLink' before adding it.
                    $this->data['pins'][$i]['tooltip']['direction']['url'] = '#';
                    $this->data['pins'][$i]['tooltip']['direction']['label'] = __('Get directions on Google Maps', 'municipio');
                }
            }
        }
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
