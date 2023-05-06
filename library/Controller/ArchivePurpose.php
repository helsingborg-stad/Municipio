<?php

namespace Municipio\Controller;

use Municipio\Helper\Location;
use Municipio\Helper\Purpose;

class ArchivePurpose extends \Municipio\Controller\Archive
{
    public function init()
    {
        add_filter('Municipio/Controller/Archive/getArchivePosts', [Location::class, 'addLocationDataToPosts'], 10, 1);

        parent::init();

        $this->data['displayArchiveLoop'] = (bool) ($this->data['archiveProps']->displayArchiveLoop ?? true);

        if (!Purpose::hasPurpose('place', get_post_type(), true)) {
            return;
        }

        $this->setupOpenStreetMap();
    }

    private function setupOpenStreetMap()
    {
        $this->data['displayOpenstreetmap'] = (bool) ($this->data['archiveProps']->displayOpenstreetmap ?? false);

        if (!$this->data['displayOpenstreetmap']) {
            return;
        }

        $this->data['displayGoogleMapsLink'] = (bool) ($this->data['archiveProps']->displayGoogleMapsLink ?? true);
        $this->data['pins'] = Location::createPinDataForPosts($this->data['posts'], $this->data['displayGoogleMapsLink']);
        $this->data['postsWithLocation'] = Location::filterPostsWithLocationData($this->data['posts']);
    }
}
