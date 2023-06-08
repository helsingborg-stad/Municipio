<?php

namespace Municipio\Controller;

use Municipio\Helper\Location as LocationHelper;
use Municipio\Helper\Purpose as PurposeHelper;

class ArchivePurpose extends \Municipio\Controller\Archive
{
    public $type;

    public function __construct()
    {
        parent::__construct();

        add_filter(
            'Municipio/Controller/Archive/getArchivePosts',
            [LocationHelper::class, 'addLocationDataToPosts'],
            10,
            1
        );

        $this->type = is_home() ? 'post' : get_post_type(get_queried_object_id());

        $this->setupOpenStreetMap();
    }

    /**
     * Legacy constructor method
     *
     * @return void
     */
    public function init()
    {
        parent::init();
    }
    /**
     * Setup OpenStreetMap-related data
     * @return void
     *
     */
    private function setupOpenStreetMap()
    {
        $this->data['displayMap'] = in_array('archives', PurposeHelper::purposeMapLocation($this->type), true);

        if ($this->data['displayMap'] && !empty($this->data['posts'])) {
            $displayGoogleMapsLink = PurposeHelper::purposeMapDisplayGoogleMapsLink($this->type);

            $this->data['pins'] = LocationHelper::createPins($this->data['posts'], $displayGoogleMapsLink);
            $this->data['postsWithLocation'] = LocationHelper::filterPostsWithLocationData($this->data['posts']);
        } else {
            $this->data['pins'] = [];
            $this->data['postsWithLocation'] = [];
        }
    }
}
