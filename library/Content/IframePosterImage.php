<?php

namespace Municipio\Content;

class IframePosterImage
{
    public function __construct()
    {
        add_filter('ComponentLibrary/Iframe/Poster', array($this, 'getPosterForIframeVideo'), 10, 1);
    }
    
    public function getPosterForIframeVideo($url) {
        $videoService = new \Municipio\Helper\VideoService($url);
        $poster = $videoService->getCoverArt();
        return $poster;
    }

}
