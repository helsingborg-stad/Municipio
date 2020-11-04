<?php

namespace Municipio\Oembed;

class YouTube extends Oembed
{
    private $playerWrapper;

    protected $playlist = array();

    public function __construct(string $url, string $html = '', bool $playerWrapper = false)
    {
        parent::__construct($url, $html);
        $this->playerWrapper = $playerWrapper;
    }

    public function output() : string
    {
        $this->getParams();

        if (!isset($this->params['v']) && !isset($this->params['list'])) {
            return $this->fallback();
        }

        if (isset($this->params['list']) && (defined('MUNICIPIO_GOOGLEAPIS_KEY') && MUNICIPIO_GOOGLEAPIS_KEY)) {
            $this->playlist = $this->getPlaylist();

            if (!isset($this->params['v'])) {
                $this->params['v'] = reset($this->playlist)->snippet->resourceId->videoId;
            }
        }

        if (!isset($this->params['v'])) {
            return $this->fallback();
        }

        $this->getThumbnail();
        // die(var_dump($this->playerMarkup()));

        return $this->playerMarkup();
    }

    public function playerMarkup()
    {
        $html = '';

        if ($this->playerWrapper) {
            if (!empty($this->playlist)) {
                $html .= '<div class="modularity-mod-player__wrapper is-playlist">';
            } else {
                $html .= '<div class="modularity-mod-player__wrapper">';
            }
        }

        $html .= '<div class="modularity-mod-player modularity-mod-player__ratio--16-9" style="background-image:url(' . $this->params['thumbnail'] . ');">';

        $html .= '<iframe type="text/html" width="1080" height="720"src="//www.youtube.com/embed/' . $this->params['v'] . '?autoplay=1&autohide=1&cc_load_policy=0&enablejsapi=1&modestbranding=1&origin=styleguide.dev&showinfo=0&autohide=1&iv_load_policy=3&rel=0" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';

        $html .= '</div>';

        if (!empty($this->playlist)) {
            $html .= '<ol class="player-playlist">';

            foreach ($this->playlist as $item) {
                $html .= '<li><a href="#" data-video-id="' . $item->snippet->resourceId->videoId . '" data-list-id="' . $this->params['list'] . '">' . $item->snippet->title . '</a></li>';
            }

            $html .= '</ul>';
        }

        if ($this->playerWrapper) {
            $html .= '</div>';
        }

        return $html;
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
            $v = str_replace('&feature=youtu.be','', $this->url);
            $v = explode('/', $v);
            $v = end($v);

            $v = (strpos($this->url, 'watch?v=')  !== false ) ?
                        str_replace('watch?v=','', $v) : $v;
            $this->params['v'] = $v;
        }

        return $this;
    }

    /**
     * Gets the video thumbnail
     * @return bool
     */
    public function getThumbnail() : bool
    {
        if (!isset($this->params['v'])) {
            $this->params['thumbnail'] = '';
            return false;
        }

        $this->params['thumbnail'] =  'https://i.ytimg.com/vi/' . $this->params['v'] . '/hqdefault.jpg';
        return true;
    }

    /**
     * Get playlist items
     * @return array Playlist
     */
    public function getPlaylist() : array
    {
        if (!defined('MUNICIPIO_GOOGLEAPIS_KEY') || !MUNICIPIO_GOOGLEAPIS_KEY || !isset($this->params['list'])) {
            return array();
        }

        $theEnd = false;
        $nextPageToken = true;

        $items = array();

        while ($nextPageToken) {
            $endpointUrl = 'https://www.googleapis.com/youtube/v3/playlistItems?playlistId=' . $this->params['list'] . '&part=snippet&maxResults=50&key=' . MUNICIPIO_GOOGLEAPIS_KEY;

            if (is_string($nextPageToken) && !empty($nextPageToken)) {
                $endpointUrl .= '&pageToken=' . $nextPageToken;
            }

            $response = wp_remote_get($endpointUrl);

            // If response code is bad return
            if (wp_remote_retrieve_response_code($response) !== 200) {
                return array();
            }

            $result = json_decode(wp_remote_retrieve_body($response));
            $items = array_merge($items, $result->items);

            $nextPageToken = isset($result->nextPageToken) ? $result->nextPageToken : false;
        }

        return $items;
    }
}
