<?php

namespace Municipio\Oembed;

class YouTube extends Oembed
{
    public function __construct($url)
    {
        parent::__construct($url);
    }

    public function output() : string
    {
        $this->getParams();
        $this->getThumbnail();

        return '
            <div class="player ratio-16-9" style="background-image:url(' . $this->params['thumbnail'] . ');">
                <a href="#video-player-' . $this->params['v'] . '" data-video-id="' . $this->params['v'] . '" data-unavailable="' . __('Video playback unavailable, enable JavaScript in your browser to watch video.', 'municipio') . '"></a>
            </div>
        ';
    }

    /**
     * Get video params
     * @return $this
     */
    public function getParams()
    {
        if (strpos($this->url, '?') !== false) {
            $url = $this->url;
            $url = explode('?', $url);
            $url = $url[1];
            $url = explode('&', $url);

            foreach ($url as $qs) {
                $qs = explode('=', $qs);
                $this->params[$qs[0]] = $qs[1];
            }
        }

        if (strpos($this->url, 'youtu.be') !== false) {
            $v = $this->url;
            $v = explode('/', $v);
            $v = end($v);

            $this->params['v'] = $v;
        }

        return $this;
    }

    /**
     * Gets the video thumbnail
     * @return void
     */
    public function getThumbnail()
    {
        $this->params['thumbnail'] = 'https://img.youtube.com/vi/' . $this->params['v'] . '/maxresdefault.jpg';
    }
}
