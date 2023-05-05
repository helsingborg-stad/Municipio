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

        if (\Municipio\Helper\Purpose::hasPurpose("place", get_post_type(), true)) {
            $this->data["displayOpenstreetmap"] = (bool) $this->data['archiveProps']->displayOpenstreetmap ?? false;
            // TODO Add setting for this in the customizer:
            $this->data["displayGoogleMapsLink"] = (bool) $this->data['archiveProps']->displayGoogleMapsLink ?? true;
// Setup pagination for posts with location
            add_filter('Municipio/Controller/Archive/getPagination', function ($pagesArray) {
                echo '<pre>' . print_r($pagesArray, true) . '</pre>';
                wp_die();
                return $pagesArray;
            });
            if ($this->data["displayOpenstreetmap"] == true) {
                // Setup pins for the map
                $this->data['pins'] = [];
                $this->data['postsWithLocation'] = [];

                foreach ($this->data['posts'] as $_post) {
                    if (
                        !empty($_post->location)
                        && !empty($_post->location["lat"])
                        && !empty($_post->location["lng"])
                    ) {
                        $this->data['postsWithLocation'][] = $_post;
                        $this->data["pins"][] = [
                            "lat" => $_post->location["lat"],
                            "lng" => $_post->location["lng"],
                            "tooltip" => [
                                "title" => $_post->postTitle,
                                "content" => $_post->postExcerpt,
                                // TODO Update this to "directions" when the new version of the OpenStreetMap component is pushed. Also check for "displayGoogleMapsLink" before adding it.
                                "direction" => [
                                    "url" => "#",
                                    "label" => __("Get directions on Google Maps", "municipio"),
                                ]
                            ]
                        ];
                    }
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
