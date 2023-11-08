<?php

namespace Municipio\Controller;

use Municipio\Helper\Location;
use Municipio\Helper\ContentType;

class ArchiveContentType extends \Municipio\Controller\Archive
{
    public function init()
    {
        add_filter('Municipio/Controller/Archive/getArchivePosts', [Location::class, 'addLocationDataToPosts'], 10, 1);

        parent::init();

        $this->data['displayArchiveLoop'] = (bool) ($this->data['archiveProps']->displayArchiveLoop ?? true);

        if (ContentType::hasContentType('place', get_post_type(), true)) {
            $this->setupOpenStreetMap();
        }

        
    }

    private function setupOpenStreetMap()
    {
        $this->data['displayOpenstreetmap'] = (bool) ($this->data['archiveProps']->displayOpenstreetmap ?? false);

        if (!$this->data['displayOpenstreetmap']) {
            return;
        }

        $this->data['displayGoogleMapsLink'] = (bool) ($this->data['archiveProps']->displayGoogleMapsLink ?? true);
        $this->data['pins'] = Location::createPins($this->data['posts'], $this->data['displayGoogleMapsLink']);
        $this->data['postsWithLocation'] = Location::filterPostsWithLocationData($this->data['posts']);
    }
}
