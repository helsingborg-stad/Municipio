<?php

namespace Municipio\Content;

class IframePosterImage
{
        /**
         * Constructor method.
         * Registers the filter for generating poster images for iframe videos.
         */
    public function __construct()
    {
        add_filter('ComponentLibrary/Iframe/Poster', array($this, 'getPosterForIframeVideo'), 10, 1);
    }

        /**
         * Generates the poster image for the given iframe video URL.
         *
         * @param string $url The URL of the iframe video.
         * @return string The URL of the generated poster image.
         */
    public function getPosterForIframeVideo(?string $url)
    {
        $videoService = new \Municipio\Helper\VideoService($url);
        $poster       = $videoService->getCoverArt();
        return $poster;
    }
}
