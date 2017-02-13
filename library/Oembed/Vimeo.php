<?php

namespace Municipio\Oembed;

class Vimeo extends Oembed
{
    public function __construct(string $url)
    {
        parent::__construct($url);
    }

    public function output() : string
    {
        $this->getParams();
        $this->getThumbnail();

        return '
            <div class="player ratio-16-9" style="background-image:url(' . $this->params['thumbnail'] . ');">
                <a href="#video-player-' . $this->params['id'] . '" data-video-id="' . $this->params['id'] . '" data-unavailable="' . __('Video playback unavailable, enable JavaScript in your browser to watch video.', 'municipio') . '"></a>
            </div>
        ';
    }

    /**
     * Get video params
     * @return $this
     */
    public function getParams()
    {
        preg_match_all('/https?:\/\/(?:www\.|player\.)?vimeo.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|video\/|)(\d+)(?:$|\/|\?)/', $this->url, $matches);
        $id = isset($matches[3][0]) ? $matches[3][0] : false;

        $this->params['id'] = $id;

        return $this;
    }

    /**
     * Gets the video thumbnail
     * @return void
     */
    public function getThumbnail()
    {
        if (!isset($this->params['id'])) {
            $this->params['thumbnail'] = '';
        }

        $requestThumb = wp_remote_get('http://vimeo.com/api/v2/video/' . $this->params['id'] . '.json');
        $requestThumb = json_decode(wp_remote_retrieve_body($requestThumb));
        $thumbnail = $requestThumb[0]->thumbnail_large;

        $this->params['thumbnail'] = $thumbnail;
    }
}
