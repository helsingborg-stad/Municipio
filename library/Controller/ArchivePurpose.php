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

        if (\Municipio\Helper\Purpose::hasPurpose("place", get_post_type(), true)) {
            // ! TODO Fetch "displayOpenStreetMap" value from the customizer for the current post types archive settings
            $this->data["displayOpenStreetMap"] = true;
            // ! TODO Fetch "mapStyle" value from the customizer for the site
            $this->data["mapStyle"] = "default";
            // ! TODO Fetch "startPosition" value from the customizer for the site
            $this->data["startPosition"] = ["lat" => "56.046029","lng" => "12.693904","zoom" => 14];

            // Setup pins for the map
            $this->data['pins'] = [];

            foreach ($this->data['posts'] as $_post) {
                $location = get_field("location", $_post->id);
                if (!empty($location) && !empty($location["lat"]) && !empty($location["lng"])) {
                    $this->data["pins"][] = [
                        "lat" => $location["lat"],
                        "lng" => $location["lng"],

                    ];
                }
            }


            // add_filter(
            //     "Municipio/Controller/Archive/getArchivePosts",
            //     [$this, "addLocationToArchivePosts"],
            //     2,
            //     1
            // );
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
